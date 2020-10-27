<?php

namespace nailfor\queue\protocol\amqp;

use nailfor\queue\packet\ControlPacket;

class Packet extends ControlPacket
{
    const TYPE_CONNECT = 'CONNECT';
    const TYPE_CONNACK = 'CONNECTED';
    const TYPE_PUBLISH = 'MESSAGE';
    
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

    public function parse($data)
    {
        $data = explode("\n\n", $data);
        
        $header = $data[0] ?? '';
        $this->message = substr($data[1] ?? '', 0, -2);

        $data = explode("\n", $header);
        foreach ($data as $parse) {
            $d = explode(':', $parse);
            if (count($d) < 2) {
                continue;
            }
            
            $key = $d[0] ?? '';
            $val = $d[1] ?? null;
            $this->$key = $val;
        }
    }
}
