<?php

namespace nailfor\queue\protocol\mqtt;

class Connect extends Packet 
{
    const EVENT = 'CONNECT';
    protected static $type = self::TYPE_CONNECT;
    
    protected $fillable = [
        'clientId',
        'username',
        'password',
        'cleanSession',
        'willTopic',
        'willMessage',
        'willQos',
        'willRetain',
        'keepAlive',
    ];
    
    protected $wrapped = [
        'clientId',
        'willTopic',
        'willMessage',
        'username',
        'password',
    ];
    
    /**
     * @return string
     */
    protected function getVariableHeader()
    {
        $protocol = $this->protocol;
        //3.1.2.1 Protocol Name
        $res[] = $this->wrapData($protocol->getProtocolIdentifierString());
        //3.1.2.2 Protocol Level
        $res[] = chr($protocol->getProtocolVersion());
        //3.1.2.3 Connect Flags
        $res[] = chr($this->getConnectFlags());
        //3.1.2.10 Keep Alive
        $res[] = $this->MostLastBit((int)$this->keepAlive);
        
        return implode("", $res);
    }

    /**
     * @return int
     */
    protected function getConnectFlags()
    {
        $connectByte = 0;
        if ($this->cleanSession) {
            $connectByte += 1 << 1;
        }
        if ($this->willTopic || $this->willMessage) {
            $connectByte += 1 << 2;
        }

        if ($this->willQos) {
            $connectByte += 1 << 3;
            // 4 TODO ?
        }

        if ($this->willRetain) {
            $connectByte += 1 << 5;
        }

        if ($this->password) {
            $connectByte += 1 << 6;
        }

        if ($this->username) {
            $connectByte += 1 << 7;
        }

        return $connectByte;
    }

    /**
     * @return string
     */
    public function getClientId()
    {
        if (is_null($this->clientId)) {
            $this->clientId = md5(microtime());
        }
        return substr($this->clientId, 0, 23);
    }
}
