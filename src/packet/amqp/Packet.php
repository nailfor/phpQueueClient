<?php

namespace nailfor\queue\packet\amqp;

use nailfor\queue\packet\ControlPacket;

class Packet extends ControlPacket
{
    protected $fillable = [];
    protected $attributes = [];
    
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
    
    protected function fill($options)
    {
        foreach ($this->fillable as $key) {
            $val = is_array($options) ? ($options[$key] ?? null) : ($options->$key ?? null);
            if ($val !== null) {
                $this->attributes[$key] = $val;
            }
        }
    }
    
    public function __get($name) 
    {
        if (property_exists($this, $name)) {
            return $this->{$name};
        }
        return $this->attributes[$name] ?? '';
    }
    
}
