<?php

namespace nailfor\queue;

use nailfor\queue\protocol\IProtocol;
use nailfor\queue\packet\ConnectionOptions;
use nailfor\queue\listeners\Acceptor;
use nailfor\queue\listeners\Connector;
use nailfor\queue\listeners\Receiver;

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

    public function __construct(string $url, Loop $loop, ReactConnector $connector, IProtocol $protocol, array $options)
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
     * @param array $listeners
     * @return PromiseInterface Resolves to a \React\Stream\Stream once a connection has been established
     */
    public function connect(array $listeners) 
    {
        $options = $this->options;
        
        $listeners = array_merge([
            Receiver::class,
            Connector::class,
            'connection' => Acceptor::class,
        ], $listeners);
        
        $promise = $this->socketConnector->connect($this->url);
        foreach($listeners as $key => $class) {
            $res = $promise->then(function($stream) use ($class) {
                $client = new $class($stream, $this->protocol, $this->options, static::$Log);
                return $client->subscribe();
            });
            
            if (!is_numeric($key)) {
                $promise = $res;
            }
        }
        
        return $promise;
    }
    
    /**
     * @return Loop
     */
    public function getLoop()
    {
        return $this->loop;
    }    
}
