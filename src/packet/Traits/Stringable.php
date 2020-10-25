<?php

namespace nailfor\queue\packet\Traits;

trait Stringable
{
    /**
     * show packet for console output
     * @return string
     */
    public function __toString() : string
    {
        if (!$this->attributes) {
            return '';
        }
        
        return (string)json_encode($this->attributes);
    }
    
    public function toArray() : array
    {
        return $this->attributes;
    }
}