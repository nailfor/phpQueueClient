<?php

namespace nailfor\queue\protocol\mqtt\receive;

use nailfor\queue\protocol\mqtt\Packet;

class Publish extends Packet 
{
    const EVENT = 'PUBLISH';
    protected static $type = self::TYPE_PUBLISH;

    protected $fillable = [
        'topic',
        'message',
        'messageId',
        'retain',
        'qos',
        'dup',
    ];
    
    protected $wrapped = [
        'topic',
    ];

    protected function addHeaderBits() : int 
    {
        $res = 0;
        if ($this->retain) {
            $res += 1;
        }
        
        if ($this->qos == 1) {
            $res += 1<<1;
        } 
        else if ($this->qos == 2) {
            $res += 1<<2;
        }

        if ($this->dup) {
            $res += 1<<3;
        }
        
        return $res;
    }
    
    public function getPayload()
    {
        $payload[] = parent::getPayload();
        $payload[] = $this->MostLastBit(static::$packetId);
        $payload[] = $this->message;
        
        return implode("", $payload);
    }
    
    public function parse($data)
    {
        $header = ord($data[0]) & 0x0F;
        $this->retain = $header & 1;
        $this->qos = ($header  & 6) >> 1;
        $this->dup = $header >> 3;
        
        $offset = $this->getOffset($data);
        
        $this->parseTopic($data, $offset);
        $this->parseMessage($data, $offset);
    }    
    
    protected function getOffset($data)
    {
        $offset = 1;
        do {
            $encodedByte = ord($data[$offset]);
            $offset++;
        } while(($encodedByte & 0x80) != 0);
        
        return $offset;
    }
    
    protected function parseTopic($data, $offset)
    {
        $headerLength = 2;
        $header = substr($data, $offset, $headerLength);
        $topicLength = $this->getInteger($header);

        $this->topic = substr($data, $offset + $headerLength, $topicLength);
    }
    
    protected function parseMessage($data, $offset)
    {
        $idlen = 0;
        if ($this->qos) {
            $idlen = 2;

            $idintifier = substr($data, 2 + strlen($this->topic) + $offset, $idlen);

            $this->messageId =  $this->getInteger($idintifier);
        }
        
        $this->message = substr($data,2 + strlen($this->topic) + $idlen + $offset);
    }
    
    public function getEventName() 
    {
        return static::EVENT;
    }
}
