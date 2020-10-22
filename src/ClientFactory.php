<?php

namespace nailfor\queue;

use nailfor\queue\protocol\Protocol;
use nailfor\queue\protocol\AMQP;

use React\Dns\Resolver\Factory as DnsResolverFactory;
use React\EventLoop\Factory as EventLoopFactory;
use React\Socket\DnsConnector;
use React\Socket\TcpConnector;

class ClientFactory
{
    public static function getClient(string $url, array $options = [], Protocol $protocol = null, string $resolverIp = '8.8.8.8') 
    {
        $loop = EventLoopFactory::create();
        $connector = self::createConnector($resolverIp, $loop);

        if (!$protocol) {
            $protocol = new AMQP();
        }
        
        return new Client($url, $loop, $connector, $protocol, $options);
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
     * Start main loop
     * @param string $url
     * @param array $options
     * @param type $errorClosure
     * @param Protocol $protocol
     * @param type $resolverIp
     */
    public static function run(string $url, array $options = [], $errorClosure = null, Protocol $protocol = null, $resolverIp = '', $log = null)
    {
        $client = static::getClient($url, $options, $protocol, $resolverIp);
        if ($log) {
            $client::$Log = $log;
        }
        
        $connection = $client->connect();

        if ($errorClosure) {
            $connection->then(null, $errorClosure);
        }

        $loop = $client->getLoop();
        $loop->run();
    }
}
