<?php

namespace nailfor\queue\protocol;

use nailfor\queue\protocol\mqtt\receive\ConnectionAck;
use nailfor\queue\protocol\mqtt\receive\Publish;
use nailfor\queue\protocol\mqtt\receive\PublishAck;
use nailfor\queue\protocol\mqtt\receive\PublishReceived;
use nailfor\queue\protocol\mqtt\Connect;
use nailfor\queue\protocol\mqtt\Disconnect;
use nailfor\queue\protocol\mqtt\Message;
use nailfor\queue\protocol\mqtt\Ping;
use nailfor\queue\protocol\mqtt\Subscribe;
use nailfor\queue\protocol\mqtt\Unsubscribe;
use nailfor\queue\protocol\mqtt\listeners\QoSLevel1;
use nailfor\queue\protocol\mqtt\listeners\QoSLevel2;

use DirectoryIterator;

class MQTT implements IProtocol 
{
    /**
     * Stored raw data from packet
     * @var type 
     */
    protected $packetData = '';
    protected $packetLength;
    protected $acknowledges = [];
    
    public function __construct() 
    {
        $path = __DIR__;
        $packets = strtolower(static::class);
        
        foreach (new DirectoryIterator("$path/mqtt/receive") as $fileInfo) {
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
            QoSLevel1::class,
            QoSLevel2::class,
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function getProtocolIdentifierString()
    {
        return 'MQTT';
    }

    /**
     * {@inheritdoc}
     */
    public function getProtocolVersion()
    {
        return 4;
    }
    
    /**
     * {@inheritdoc}
     */
    public function next($data)
    {
        while (isset($data[1])) {
            if (!$this->packetData) {
                //3.2.1 Fixed header
                $offset = 1;
                $this->packetLength = $offset + $this->getLength($data, $offset);
            }

            $this->packetData .= $data;
            if (strlen($this->packetData) < $this->packetLength) {
                //get next chunk
                break;
            }
            
            $data = substr($this->packetData, $this->packetLength);
            $this->packetData = substr($this->packetData, 0, $this->packetLength);

            yield $this->parse();
        }
    }
    
    /**
     * Parse data and get Packet
     * @return Message
     */
    protected function parse() {
        $type = ord($this->packetData[0]) >> 4;
        
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
     * Calculate length of packet
     * @param string $remainingData
     * @param int $offset
     * @return int length
     * @throws type
     */
    protected function getLength(string $remainingData, int &$offset) : int
    {
       $multiplier = 1;
       $value = 0;
       $offset = 1;

       do{
            $encodedByte = ord($remainingData[$offset]);
            $offset ++;
            $value += ($encodedByte & 0x7F) * $multiplier;
            $multiplier *= 0x80;
            if ($multiplier > 0x10000000){
               throw \RuntimeException('Malformed Remaining Length');
            }

       }while (($encodedByte & 0x80) != 0);
       
       return $value;
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
     * Get class connect
     * @return string
     */
    public function getDisconnect() : string
    {
        return Disconnect::class;
    }

    
    /**
     * Get class publish
     * @return string
     */
    public function getPublish() : string
    {
        return Publish::class;
    }
    
    /**
     * Get class publish acknowledge
     * @return string
     */
    public function getPublishAck() : string
    {
        return PublishAck::class;
    }
    
    /**
     * Get class publish acknowledge
     * @return string
     */
    public function getPublishRec() : string
    {
        return PublishReceived::class;
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
    
    /**
     * Get class unsubscribe
     * @return string
     */
    public function getUnsubscribe() : string
    {
        return Unsubscribe::class;
    }
    
    /**
     * Get class ping
     * @return string
     */
    public function getPing() : string
    {
        return Ping::class;
    }
    
}