<?php

namespace nailfor\queue;

use nailfor\queue\protocol\Protocol;
use nailfor\queue\packet\Publish;
use nailfor\queue\packet\PublishAck;

use React\Dns\Resolver\Factory as DnsResolverFactory;
use React\EventLoop\Factory as EventLoopFactory;
use React\Socket\DnsConnector;
use React\Socket\SecureConnector;
use React\Socket\TcpConnector;

class ClientFactory
{
    public static $client;
    public static $connection;
    protected static $protocol;
    
    protected static function setClient(string $url, array $options = [], Protocol $protocol = null, string $resolverIp = '8.8.8.8') {
        $loop = EventLoopFactory::create();
        $connector = self::createConnector($resolverIp, $loop);

        if (!$protocol) {
            $protocol = new AMQP();
        }
        static::$protocol = $protocol;
        
        static::$client =  new Client($url, $loop, $connector, $protocol, $options);
    }

    /**
     * Create connector with DNSResolverFactory
     * @param type $resolverIp
     * @param type $loop
     * @return DnsConnector
     */
    protected static function createConnector($resolverIp, $loop)
    {
        $dnsResolverFactory = new DnsResolverFactory();
        $resolver = $dnsResolverFactory->createCached($resolverIp, $loop);

        return new DnsConnector(new TcpConnector($loop), $resolver);
    }
    
    
    
    /**
     * Start maim loop
     * @param string $url
     * @param array $options
     * @param type $errorClosure
     * @param Protocol $protocol
     * @param type $resolverIp
     */
    public static function run(string $url, array $options = [], $errorClosure = null, Protocol $protocol = null, $resolverIp = '')
    {
        static::setClient($url, $options, $protocol, $resolverIp);
        
        static::$connection = static::$client->connect();
        if ($errorClosure) {
            static::$connection->then(null, $errorClosure);
        }
        
        $topics = $options['topics'] ?? [];
        static::$protocol->setTopics($topics, static::$connection, static::$client);

        $loop = static::$client->getLoop();
        $loop->run();
    }
}
