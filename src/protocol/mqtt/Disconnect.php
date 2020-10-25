<?php

namespace nailfor\queue\protocol\mqtt;

class Disconnect extends Packet 
{
    const EVENT = 'DISCONNECT';
    protected static $type = self::TYPE_DISCONNECT;
}
