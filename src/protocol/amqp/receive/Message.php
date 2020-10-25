<?php

namespace nailfor\queue\protocol\amqp\receive;

use nailfor\queue\protocol\amqp\Packet;

class Message extends Packet 
{
    const EVENT = 'PUBLISH';
    protected static $type = self::TYPE_PUBLISH;
}
