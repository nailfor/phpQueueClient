<?php

namespace nailfor\queue\protocol\mqtt\receive;

use nailfor\queue\protocol\mqtt\Packet;

class UnsubscribeAck extends Packet 
{
    const EVENT = 'UNSUBSCRIBE_ACKNOWLEDGE';
    protected static $type = self::TYPE_UNSUBACK;
    
    public function parse($data)
    {
        $this->id = $this->getInteger(substr($data, 2, 2));
    }    
}
