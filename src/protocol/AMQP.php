<?php

namespace nailfor\queue\protocol;

use nailfor\queue\packet\amqp\Connect;
use nailfor\queue\packet\amqp\ConnectionAck;
use nailfor\queue\packet\amqp\Subscribe;
use nailfor\queue\packet\amqp\Message;

class AMQP implements Protocol 
{
    static $rawData = '';
    
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
    protected function setEvent($stream, $params)
    {
        $events = $params['events'] ?? [];
        
        foreach ($events as $event => $callback) {
            $stream->on($event, $callback);
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function setTopics($topics, $connection, $client)
    {
        foreach ($topics as $topic=>$params) {
            $connection->then(function ($stream) use ($client, $topic, $params) {
                $subscribe = new Subscribe(['destination' => $topic]);
                $client->sendPacketToStream($stream, $subscribe);
                
                $this->setEvent($stream, $params);
            });
            
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function next($data)
    {
        static::$rawData .= $data;
        
        if ($data{-2} == "\x00" && $data{-1} == "\n") {
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
    
    
    public function connect()
    {
        return Connect::class;
    }
    
    public function connectionAck()
    {
        return ConnectionAck::class;
    }
    
}