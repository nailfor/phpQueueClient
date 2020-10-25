<?php

namespace nailfor\queue\protocol\mqtt;

class Ping extends Packet 
{
    const EVENT = 'PING';
    protected static $type = self::TYPE_PINGREQ;
}
