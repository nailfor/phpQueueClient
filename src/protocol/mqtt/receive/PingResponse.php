<?php

namespace nailfor\queue\protocol\mqtt\receive;

use nailfor\queue\protocol\mqtt\Packet;

class PingResponse extends Packet 
{
    const EVENT = 'PONG';
    protected static $type = self::TYPE_PINGRESP;
}
