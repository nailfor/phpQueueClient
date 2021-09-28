<?php

namespace nailfor\queue\protocol;

use nailfor\queue\protocol\amqp\receive\ConnectionAck;
use nailfor\queue\protocol\amqp\receive\Message;
use nailfor\queue\protocol\amqp\Connect;
use nailfor\queue\protocol\amqp\Subscribe;

use DirectoryIterator;

class AMQP implements IProtocol 
{
    /**
     * Stored raw data from packet
     * @var type 
     */
    protected $packetData = '';

    public function __construct() 
    {
        $path = __DIR__;
        $packets = strtolower(static::class);
        
        foreach (new DirectoryIterator("$path/amqp/receive") as $fileInfo) {
            if($fileInfo->isDot()) {
                continue;
            }
            $name = $fileInfo->getBasename('.php');
            $class = "$packets\\receive\\$name";
            $this->acknowledges[$class::getType()] = $class;
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function getListeners(): array 
    {
        return [
        ];
    }
    
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
     * {@inheritdoc}
     */
    public function next($data)
    {
        $this->packetData .= $data;
        
        if ($data[-2] == "\x00" && $data[-1] == "\n") {
            $packet = $this->parse();
            if ($packet) {
                yield $packet;
            }
        }
    }
    
    /**
     * Parse data and get Packet
     * @return Packet
     */
    protected function parse() {
        $data = explode("\n", $this->packetData);
        $type = array_shift($data);
        
        $packetClass = $this->acknowledges[$type] ?? 0;
        if (!$packetClass) {
            return;
        }
        
        $packet = new $packetClass([], $this);
        $packet->parse($this->packetData);
                
        $this->packetData = '';
        
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
     * Get class publish
     * @return string
     */
    public function getPublish() : string
    {
        return Message::class;
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