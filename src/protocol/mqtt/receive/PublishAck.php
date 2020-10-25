<?php

namespace nailfor\queue\protocol\mqtt\receive;

use nailfor\queue\protocol\mqtt\Packet;

class PublishAck extends Packet
{
    //qos level = 1

    const EVENT = 'PUBLISH_ACKNOWLEDGE';
    protected static $type = self::TYPE_PUBACK;
    
    protected $fillable = [
        'topic',
        'message',
        'messageId',
        'retain',
        'qos',
        'dup',
    ];    
    
    /**
     * @return string
     */
    protected function getVariableHeader()
    {
        //3.4.2 Variable header
        return $this->MostLastBit($this->messageId);
    }    
    
    public function parse($data)
    {
        $this->messageId = $this->getInteger(substr($data, 2, 2));
    }
  
}
