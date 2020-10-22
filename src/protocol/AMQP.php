<?php

namespace nailfor\queue\protocol;

use nailfor\queue\packet\amqp\Connect;
use nailfor\queue\packet\amqp\ConnectionAck;
use nailfor\queue\packet\amqp\Subscribe;
use nailfor\queue\packet\amqp\Message;

class AMQP implements Protocol 
{
    /**
     * Stored raw data from packet
     * @var type 
     */
    protected static $rawData = '';
    
    /**
     * {@inheritdoc}
     */
    public function getProtocolIdentifierString()
    {
        return 'AMQP';
    }

    /**
     * {@inheritdoc}
     */
    public function getProtocolVersion()
    {
        return 1.1;
    }
    
    /**
     * Set listener on event
     * @param type $stream
     * @param type $params
     */
    public function setEvent($stream, $params)
    {
        $events = $params['events'] ?? [];
        
        foreach ($events as $event => $callback) {
            $stream->on($event, $callback);
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function next($data)
    {
        static::$rawData .= $data;
        
        if ($data[-2] == "\x00" && $data[-1] == "\n") {
            $data = static::$rawData;
            static::$rawData = '';
            
            $packet = $this->parse($data);
            if ($packet) {
                yield $packet;
            }
        }
    }
    
    /**
     * Parse data and get Packet
     * @param type $rawData
     * @return Message
     */
    protected function parse($rawData) {
        $data = explode("\n", $rawData);
        $type = array_shift($data);
        $packet = '';
        switch ($type) {
            case 'CONNECTED':
                return;
            case 'MESSAGE':
                $packet = new Message($data);
                break;
        }
        return $packet;
    }
    
    
    /**
     * Get class connect
     * @return string
     */
    public function getConnect() : string
    {
        return Connect::class;
    }
    
    
    /**
     * Get class connectAck
     * @return string
     */
    public function getConnectionAck() : string
    {
        return ConnectionAck::class;
    }
    
    /**
     * Get class subscribe
     * @return string
     */
    public function getSubscribe() : string
    {
        return Subscribe::class;
    }
}