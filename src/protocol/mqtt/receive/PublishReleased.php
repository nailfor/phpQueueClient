<?php

namespace nailfor\queue\protocol\mqtt\receive;

class PublishReleased extends PublishAck 
{
    //qos level = 2
    
    const EVENT = 'PUBLISH_RELEASED';
    protected static $type = self::TYPE_PUBREL;
    
    //3.6.1 Fixed header
    protected function addHeaderBits() : int
    {
        return 2;
    }
    
}
