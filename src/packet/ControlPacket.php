<?php

namespace nailfor\queue\packet;

abstract class ControlPacket
{

    protected $payload = '';

    protected $identifier;
    
    public $rawData = '';

    public function parse()
    {
        return $this;
    }

    /**
     * @param Version $version
     * @param string $rawInput
     * @return static
     */
    public static function parsePacket(Version $version, $rawInput)
    {
        static::checkRawInputValidControlPackageType($rawInput);
        
        $packet = new static($version);
        $packet->rawData = $rawInput;
        $packet->parse();
        return $packet;
    }

    protected static function checkRawInputValidControlPackageType($rawInput)
    {
        $packetType = ord($rawInput[0]) >> 4;
        if ($packetType !== static::getControlPacketType()) {
            throw new \RuntimeException('raw input is not valid for this control packet');
        }
    }

    /** @return int */
    public static function getControlPacketType() {
        throw new \RuntimeException('you must overwrite getControlPacketType()');
    }

    protected function getPayloadLength()
    {
        return strlen($this->getPayload());
    }

    public function getPayload()
    {
        return $this->payload;
    }

    protected function getRemainingLength()
    {
        return strlen($this->getVariableHeader()) + $this->getPayloadLength();
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
     * @param $stringToAdd
     */
    public function addRawToPayLoad($stringToAdd)
    {
        $this->payload .= $stringToAdd;
    }

    /**
     * @param $fieldPayload
     */
    public function addLengthPrefixedField($fieldPayload)
    {
        $return = $this->getLengthPrefixField($fieldPayload);
        $this->addRawToPayLoad($return);
    }

    public function getLengthPrefixField($fieldPayload)
    {
        $stringLength = strlen($fieldPayload);
        $msb = $stringLength >> 8;
        $lsb = $stringLength % 256;
        $return = chr($msb);
        $return .= chr($lsb);
        $return .= $fieldPayload;

        return $return;
    }

    public function get()
    {
        return $this->getFixedHeader() .
               $this->getVariableHeader() .
               $this->getPayload();
    }

    /**
     * @param $byte1
     * @return $byte1 unmodified
     */
    protected function addReservedBitsToFixedHeaderControlPacketType($byte1)
    {
        return $byte1;
    }
}
