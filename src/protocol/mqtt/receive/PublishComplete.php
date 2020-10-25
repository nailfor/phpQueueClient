<?php

namespace nailfor\queue\protocol\mqtt\receive;

class PublishComplete extends PublishAck 
{
    //qos level = 2
    
    const EVENT = 'PUBLISH_COMPLETE';
    protected static $type = self::TYPE_PUBCOMP;
}
