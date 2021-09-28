<?php

namespace nailfor\queue\protocol\mqtt\listeners;

use Closure;
use nailfor\queue\protocol\mqtt\receive\PublishReceived;
use nailfor\queue\protocol\mqtt\receive\PublishComplete;
use nailfor\queue\protocol\mqtt\receive\PublishReleased;

class QoSLevel2 extends QoSLevel1
{
    const QOS_LEVEL = 2;

    protected function getPacketClass() 
    {
        return PublishReceived::class;
    }
    
    public function subscribe()
    {
        /*
         * Publish->
         * <-PublishReceived
         * PublishReleased->
         * <-PublishComplete
         */
        
        $this->stream->on('PUBLISH_RELEASED', Closure::fromCallable([$this, 'onReleased']));
        $this->stream->on('PUBLISH_RECEIVED', Closure::fromCallable([$this, 'onReceived']));

        return parent::subscribe();
    }
    
    protected function onReleased($packet)
    {
        $packetClass = PublishComplete::class;
        $this->onData($packetClass, $packet);
    }
    
    protected function onReceived($packet)
    {
        $packetClass = PublishReleased::class;
        $this->onData($packetClass, $packet);
    }
}
