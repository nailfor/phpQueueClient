<?php

namespace nailfor\queue\protocol\amqp\receive;

use nailfor\queue\protocol\amqp\Packet;

class ConnectionAck extends Packet 
{
    const EVENT = 'CONNECT_ACKNOWLEDGE';
    protected static $type = self::TYPE_CONNACK;
}
