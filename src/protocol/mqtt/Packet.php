<?php

namespace nailfor\queue\protocol\mqtt;

use nailfor\queue\packet\ControlPacket;

class Packet extends ControlPacket
{
    protected static $type;
    protected $appends;
    protected $wrapped;
    protected $protocol;
    
    const TYPE_CONNECT = 1;
    const TYPE_CONNACK = 2;
    const TYPE_PUBLISH = 3;
    const TYPE_PUBACK = 4;
    const TYPE_PUBREC = 5;
    const TYPE_PUBREL = 6;
    const TYPE_PUBCOMP = 7;
    const TYPE_SUBSCRIBE = 8;
    const TYPE_SUBACK = 9;
    const TYPE_UNSUBSCRIBE = 10;
    const TYPE_UNSUBACK = 11;
    const TYPE_PINGREQ = 12;
    const TYPE_PINGRESP = 13;
    const TYPE_DISCONNECT = 14;    
    
    
    public function __construct($options, $protocol = null) 
    {
        $this->fill($options);
        $this->protocol = $protocol;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getPayload()
    {
        $payload = [];
        $wrapped = $this->wrapped ?? [];
        
        foreach($wrapped as $field) {
            $val = $this->$field;
            if ($val) {
                $payload[] = $this->wrapData($val);
            }
        }
        
        $append = $this->appends ?? [];
        foreach($append as $field) {
            $val = $this->$field;
            $payload[] = chr($val);
        }

        return implode("", $payload);
    }

    /**
     * {@inheritdoc}
     */
    protected function getFixedHeader()
    {
        // Figure 3.1.1
        $type = static::$type << 4;
        $type += $this->addHeaderBits();
        
        $res[] = chr($type);
        $res[] = chr($this->getRemainingLength());

        return implode("", $res);
    }
    
    protected function addHeaderBits() : int
    {
        return 0;
    }

    protected function getRemainingLength()
    {
        $lenHeader = strlen($this->getVariableHeader());
        $lenPayload = strlen($this->getPayload());
        
        return  $lenHeader + $lenPayload;
    }

    protected function wrapData($data)
    {
        $res[] = $this->length($data);
        $res[] = $data;

        return implode("", $res);
    }
    
    protected function length($data)
    {
        $len = strlen((string)$data);
        
        return $this->MostLastBit($len);
    }
    
    protected function MostLastBit($data)
    {
        $msb = $data >> 8;
        $lsb = $data & 0xFF;
        $res[] = chr($msb);
        $res[] = chr($lsb);
        
        return implode("", $res);
    }
    
    /**
     * Calculate length
     * @param string $remainingData
     * @return int length
     * @throws type
     */
    protected function getInteger(string $data) : int
    {
       $value = 0;
       for ($i=0; $i<strlen($data); $i++)
       {
           $multiplier = 1 << ($i*8);
           $k = strlen($data) - $i - 1;
           $value += ord($data[$k]) * $multiplier;
       }
       
       return $value;
    }    
}
