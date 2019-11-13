<?php

namespace nailfor\queue;

use nailfor\queue\protocol\Protocol;
use nailfor\queue\protocol\Violation;
use nailfor\queue\packet\ConnectionOptions;
use nailfor\queue\packet\ControlPacket;

use React\EventLoop\LoopInterface as Loop;
use React\EventLoop\Timer\Timer;
use React\Promise\Deferred;
use React\Promise\FulfilledPromise;
use React\Promise\PromiseInterface;
use React\Socket\ConnectionInterface as Connection;
use React\Socket\ConnectorInterface as ReactConnector;

class Client
{
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
        // Set default connection options, if none provided
        $options = $this->options;

        $promise = $this->socketConnector->connect($this->url);
        $promise->then(function(Connection $stream) {
            $this->listenForPackets($stream);
        });
        $connection = $promise->then(function(Connection $stream) use ($options) {
            return $this->sendConnectPacket($stream, $options);
        });
        $connection->then(function(Connection $stream) use ($options) {
            return $this->keepAlive($stream, $options->keepAlive);
        });

        
        return $connection;
    }

    protected function listenForPackets(Connection $stream)
    {
        $stream->on('data', function($rawData) use ($stream) {
            try {
                //echo "====================\n$rawData\n====================\n";
                
                foreach ($this->protocol->next($rawData) as $packet) {
                    $event = $packet::EVENT;
                    /*
                    if (method_exists($packet, 'getTopic')) {
                        $topic = $packet->getTopic();
                        $event .= ":$topic";
                    }
                    //echo $event."\n";
                     */
                    $stream->emit($event, [$packet]);
                }
            }
            catch (Violation $e) {
                //TODO Actually, the spec says to disconnect if you receive invalid data.
                $stream->emit('INVALID', [$e]);
            }
        });
    }

    protected function keepAlive(Connection $stream, $keepAlive)
    {
        if($keepAlive > 0) {
            $interval = $keepAlive / 2;

            $this->getLoop()->addPeriodicTimer($interval, function(Timer $timer) use ($stream) {
                $packet = new PingRequest($this->version);
                $this->sendPacketToStream($stream, $packet);
            });
        }

        return new FulfilledPromise($stream);
    }

    /**
     * @return \React\Promise\Promise
     */
    protected function sendConnectPacket(Connection $stream, ConnectionOptions $options) 
    {
        $connect = $this->protocol->connect();
        $packet = new $connect($options);
        //$message = $packet->get();
        //echo MessageHelper::getReadableByRawString($message);

        $deferred = new Deferred();
        
        $connectionAck = $this->protocol->connectionAck();
        $stream->on($connectionAck::EVENT, function($message) use ($stream, $deferred) {
            $deferred->resolve($stream);
        });

        $this->sendPacketToStream($stream, $packet);

        return $deferred->promise();
    }

    public function sendPacketToStream(Connection $stream, ControlPacket $controlPacket)
    {
        $message = $controlPacket->get();
        //echo "send:\t\t" . get_class($controlPacket) . "\n-message:\n>>$message<<\n";
        return $stream->write($message);
    }

    public function disconnect(Connection $stream)
    {
        $packet = new Disconnect($this->version);
        $this->sendPacketToStream($stream, $packet);
        $this->getLoop()->stop();

        return new FulfilledPromise($stream);
    }

    /**
     * @return Loop
     */
    public function getLoop()
    {
        return $this->loop;
    }
}
