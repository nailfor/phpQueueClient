<?php

namespace nailfor\queue\packet;

abstract class ControlPacket
{
    use Traits\HasAttributes;
    use Traits\Stringable;
    
    /**
     * Counter for packets
     * @var int 
     */
    static $packetId = 0;


    const EVENT = 'none';
    
    /**
     * Stored payload string
     * @var string
     */
    protected $payload = '';
    protected static $type;

    /**
     * Get payload without headers
     * @return type
     */
    public function getPayload()
    {
        return $this->payload;
    }
    
    public static function getType() 
    {
        return static::$type;
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

    public function getName()
    {
        $path = explode('\\', static::class);
        return array_pop($path);
    }
    
    public function getEventName() 
    {
        return static::EVENT;
    }
    
    public function parse($data)
    {
        return;
    }
    
    public function incrasePacket()
    {
        static::$packetId++;
    }
}
