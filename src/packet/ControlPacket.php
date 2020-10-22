<?php

namespace nailfor\queue\packet;

abstract class ControlPacket
{
    use Traits\HasAttributes;
    use Traits\Stringable;
    
    /**
     * Stored payload string
     * @var string
     */
    protected $payload = '';
    

    /**
     * Get payload without headers
     * @return type
     */
    public function getPayload()
    {
        return $this->payload;
    }
    
    /**
     * Get packet payload with headers
     * @return type
     */
    public function get()
    {
        return $this->getFixedHeader() .
               $this->getVariableHeader() .
               $this->getPayload();
    }    

    /**
     * @return string
     */
    protected function getFixedHeader()
    {
        return '';
    }

    /**
     * @return string
     */
    protected function getVariableHeader()
    {
        return '';
    }

    
    /**
     * show packet for console output
     * @return type
     */
    public function __toString() 
    {
        return addslashes($this->payload);
    }    
}
