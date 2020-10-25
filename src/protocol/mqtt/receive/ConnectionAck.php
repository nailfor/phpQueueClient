<?php

namespace nailfor\queue\protocol\mqtt\receive;

use nailfor\queue\protocol\mqtt\Packet;

class ConnectionAck extends Packet 
{
    const EVENT = 'CONNECT_ACKNOWLEDGE';
    protected static $type = self::TYPE_CONNACK;
    
    protected static $answers = [
        0 => 'Connection Accepted',
        1 => 'unacceptable protocol version',
        2 => 'identifier rejected',
        3 => 'Server unavailable',
        4 => 'bad user name or password',
        5 => 'not authorized',
    ];
    
    public function parse($data)
    {
        //3.2.1 Fixed header
        //skip 2 byte

        //3.2.2 Variable header
        $session = ord(substr($data,2,1));
        $code = ord(substr($data,3,1));
        
        //3.2.2.2 Session Present
        $this->session = $session & 1;
        $this->code = $code;
        $this->answer = static::$answers[$code] ?? 'unknown';
    }
}
