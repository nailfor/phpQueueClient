<?php

namespace nailfor\queue\protocol\mqtt;

class Subscribe extends Packet 
{
    const EVENT = 'SUBSCRIBE';
    protected static $type = self::TYPE_SUBSCRIBE;
    
    protected $fillable = [
        'topic',
        'qos',
    ];

    //3.8.3 Payload
    protected $wrapped = [
        'topic',
    ];
    
    protected $appends = [
        'qos',
    ];
    
    protected function addHeaderBits() : int
    {
        //3.8.1 Fixed header
        return 2;
    }
    
    /**
     * @return string
     */
    protected function getVariableHeader()
    {
        //3.8.2 Variable header
        return $this->MostLastBit(static::$packetId);
    }    
}
