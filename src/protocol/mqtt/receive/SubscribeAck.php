<?php

namespace nailfor\queue\protocol\mqtt\receive;

use nailfor\queue\protocol\mqtt\Packet;

class SubscribeAck extends Packet 
{
    const EVENT = 'SUBSCRIBE_ACKNOWLEDGE';
    protected static $type = self::TYPE_SUBACK;
    
    public function parse($data)
    {
        $this->id = $this->getInteger(substr($data, 2, 2));
                
        $qos = $data[4] ?? '';
        $this->qos = ord($qos);
    }    
}
