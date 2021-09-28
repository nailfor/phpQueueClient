<?php

namespace nailfor\queue\protocol\mqtt\listeners;

use Closure;
use nailfor\queue\listeners\Listener;

class QoSLevel1 extends Listener
{
    const QOS_LEVEL = 1;

    protected function getPacketClass() 
    {
        return $this->protocol->getPublishAck();
    }
    
    public function subscribe()
    {
        $publishClass = $this->protocol->getPublish();
        $this->stream->on($publishClass::EVENT, Closure::fromCallable([$this, 'onEvent']));
        
        return $this->stream;
    }
    
    protected function onEvent($packet)
    {
        if ($packet->qos != static::QOS_LEVEL) {
            return;
        }
        
        $packetClass = $this->getPacketClass();
        $this->onData($packetClass, $packet);
    }
    
    protected function onData($packetClass, $packet)
    {
        $packetAns = new $packetClass($packet, $this->protocol);
        $this->send($packetAns);
    }
}
