<?php

namespace nailfor\queue\protocol\amqp;

use nailfor\queue\packet\ControlPacket;

class Packet extends ControlPacket
{
    public function __construct() 
    {
        $payload = [];
        $payload[] = $this->command;
        $payload[] = $this->dumpHeaders();
        $payload[] = "\x00";
        
        $this->payload = implode("\n", $payload);
    }
    
    protected function dumpHeaders()
    {
        $dumped = '';

        foreach ($this->attributes as $name => $value) {
            $name   = $this->escapeHeaderValue($name);
            $value  = $this->escapeHeaderValue($value);

            $dumped .= "$name:$value\n";
        }

        return $dumped;
    }

    protected function escapeHeaderValue($value)
    {
        return strtr($value, [
            "\n"    => '\n',
            ':'     => '\c',
            '\\'    => '\\\\',
        ]);
    }

}
