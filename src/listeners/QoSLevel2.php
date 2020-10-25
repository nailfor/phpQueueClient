<?php

namespace nailfor\queue\listeners;

use Closure;

class QoSLevel2 extends QoSLevel1
{
    const QOS_LEVEL = 2;

    protected function getPacketClass() 
    {
        return \nailfor\queue\protocol\mqtt\receive\PublishReceived::class;
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
        $packetClass = \nailfor\queue\protocol\mqtt\receive\PublishComplete::class;
        $this->onData($packetClass, $packet);
    }
    
    protected function onReceived($packet)
    {
        $packetClass = \nailfor\queue\protocol\mqtt\receive\PublishReleased::class;
        $this->onData($packetClass, $packet);
    }
}
