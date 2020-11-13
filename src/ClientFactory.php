<?php

namespace nailfor\queue;

use nailfor\queue\protocol\IProtocol;
use nailfor\queue\protocol\AMQP;

use nailfor\queue\listeners\Disconnector;
use nailfor\queue\listeners\Keeper;
use nailfor\queue\listeners\Pinger;
use nailfor\queue\listeners\Sender;
use nailfor\queue\listeners\Subscriber;
use nailfor\queue\listeners\Unsubscriber;


use React\Dns\Resolver\Factory as DnsResolverFactory;
use React\EventLoop\Factory as EventLoopFactory;
use React\Socket\DnsConnector;
use React\Socket\TcpConnector;

class ClientFactory
{
    /**
     * Create client for given protocol
     * @param string $url
     * @param array $options
     * @param IProtocol $protocol
     * @param string $resolverIp
     * @return Client
     */
    public static function getClient(string $url, array $options = [], IProtocol $protocol = null, string $resolverIp = '') 
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
        $connector = new TcpConnector($loop);
        if (!$resolverIp) {
            return $connector;
        }
        
        $dnsResolverFactory = new DnsResolverFactory();
        $resolver = $dnsResolverFactory->createCached($resolverIp, $loop);

        return new DnsConnector($connector, $resolver);
    }
    
    /**
     * Subscribe and get messages
     * @param string $url
     * @param array $options
     * @param type $errorClosure
     * @param Protocol $protocol
     * @param type $resolverIp
     */
    public static function run(string $url, array $options = [], $errorClosure = null, IProtocol $protocol = null, $resolverIp = '', $log = null)
    {
        $instant = new static;
        $instant->ClientRun([
            Keeper::class,
            Subscriber::class,
        ], $url, $options, $errorClosure, $protocol, $resolverIp, $log);
    }
    
    /**
     * Unsubscribe from topics
     * @param string $url
     * @param array $options
     * @param type $errorClosure
     * @param IProtocol $protocol
     * @param type $resolverIp
     * @param type $log
     */
    public static function unsubscribe(string $url, array $options = [], $errorClosure = null, IProtocol $protocol = null, $resolverIp = '', $log = null) 
    {
        $instant = new static;
        $instant->ClientRun([
            'unsubscriber' => Unsubscriber::class,
            Disconnector::class,
        ], $url, $options, $errorClosure, $protocol, $resolverIp, $log);
    }
    
    /**
     * Publish message on topics
     * @param string $url
     * @param array $options
     * @param type $errorClosure
     * @param IProtocol $protocol
     * @param type $resolverIp
     * @param type $log
     */
    public static function publish(string $url, array $options = [], $errorClosure = null, IProtocol $protocol = null, $resolverIp = '', $log = null) 
    {
        $instant = new static;
        $instant->ClientRun([
            'sender' => Sender::class,
            Disconnector::class,
        ], $url, $options, $errorClosure, $protocol, $resolverIp, $log);
    }
    
    /**
     * Ping server
     * @param string $url
     * @param array $options
     * @param type $errorClosure
     * @param IProtocol $protocol
     * @param type $resolverIp
     * @param type $log
     */
    public static function ping(string $url, array $options = [], $errorClosure = null, IProtocol $protocol = null, $resolverIp = '', $log = null) 
    {
        $instant = new static;
        $instant->ClientRun([
            Pinger::class,
            Disconnector::class,
        ], $url, $options, $errorClosure, $protocol, $resolverIp, $log);
    }
    
    /**
     * Connect to server and run events
     * @param array $listeners
     * @param string $url
     * @param array $options
     * @param type $errorClosure
     * @param IProtocol $protocol
     * @param type $resolverIp
     * @param type $log
     */
    protected function ClientRun(array $listeners, string $url, array $options = [], $errorClosure = null, IProtocol $protocol = null, $resolverIp = '', $log = null) 
    {
        $client = static::getClient($url, $options, $protocol, $resolverIp);
        if ($log) {
            $client::$Log = $log;
        }
        
        $connection = $client->connect($listeners);

        if ($errorClosure) {
            $connection->then(null, $errorClosure);
        }

        $loop = $client->getLoop();
        $loop->run();
    }
}
