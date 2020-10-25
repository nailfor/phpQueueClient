<?php

namespace nailfor\queue\protocol\mqtt\receive;

class PublishReceived extends PublishAck 
{
    //qos level = 2
    
    const EVENT = 'PUBLISH_RECEIVED';
    protected static $type = self::TYPE_PUBREC;
}
