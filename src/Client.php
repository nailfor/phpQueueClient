<?php

namespace nailfor\queue;

use nailfor\queue\protocol\Protocol;
use nailfor\queue\packet\ConnectionOptions;
use nailfor\queue\listeners\Connector;
use nailfor\queue\listeners\Keeper;
use nailfor\queue\listeners\Receiver;
use nailfor\queue\listeners\Subscriber;

use Closure;
use React\EventLoop\LoopInterface as Loop;
use React\Promise\FulfilledPromise;
use React\Socket\ConnectionInterface as Connection;
use React\Socket\ConnectorInterface as ReactConnector;

class Client
{
    public static $Log;

    protected $url;
    protected $loop;
    protected $socketConnector;
    protected $protocol;
    protected $options;

    public function __construct(string $url, Loop $loop, ReactConnector $connector, Protocol $protocol, array $options)
    {
        $this->url = $url;
        $this->protocol = $protocol;
        $this->socketConnector = $connector;
        $this->loop = $loop;

        $this->options = new ConnectionOptions($options);
    }

    /**
     * Creates a new connection
     *
     *
     * @return PromiseInterface Resolves to a \React\Stream\Stream once a connection has been established
     */
    public function connect() 
    {
        $options = $this->options;

        $promise = $this->socketConnector->connect($this->url);
        
        $promise->then(Closure::fromCallable([$this, 'onData']));
        
        $connection = $promise->then(Closure::fromCallable([$this, 'sendConnectPacket']));
        $connection->then(Closure::fromCallable([$this, 'keepAlive']));
        $connection->then(Closure::fromCallable([$this, 'subScribe']));
        
        return $connection;
    }
    
    /**
     * @return Loop
     */
    public function getLoop()
    {
        return $this->loop;
    }    
    
    protected function onData(Connection $stream)
    {
        $client = new Receiver($stream, $this->protocol, $this->options, static::$Log);
        return $client->subscribe();
    }

    /**
     * Periodic send ping to server
     * @param Connection $stream
     * @return FulfilledPromise
     */
    protected function keepAlive(Connection $stream)
    {
        $client = new Keeper($stream, $this->protocol, $this->options, static::$Log);
        return $client->subscribe();
    }

    /**
     * subscribe on topics
     * @param type $connection
     */
    protected function subScribe(Connection $stream) 
    {
        $client = new Subscriber($stream, $this->protocol, $this->options, static::$Log);
        return $client->subscribe();
    }
    
    /**
     * @return \React\Promise\Promise
     */
    protected function sendConnectPacket(Connection $stream) 
    {
        $client = new Connector($stream, $this->protocol, $this->options, static::$Log);
        return $client->subscribe();
    }
}
