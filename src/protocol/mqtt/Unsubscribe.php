<?php

namespace nailfor\queue\protocol\mqtt;

class Unsubscribe extends Packet 
{
    const EVENT = 'UNSUBSCRIBE';
    protected static $type = self::TYPE_UNSUBSCRIBE;
    
    protected $fillable = [
        'topic',
    ];

    //3.10.3 Payload
    protected $wrapped = [
        'topic',
    ];
    
    //3.10.1 Fixed header
    protected function addHeaderBits() : int
    {
        return 2;
    }
    
    /**
     * @return string
     */
    protected function getVariableHeader()
    {
        //3.10.2 Variable header
        return $this->MostLastBit(static::$packetId);
    }    
    
}
