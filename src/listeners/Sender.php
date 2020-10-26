<?php

namespace nailfor\queue\listeners;

use Closure;
use React\Promise\Deferred;

class Sender extends Subscriber
{
    protected $deferred;
    
    protected function getPacketClass() 
    {
        return $this->protocol->getPublish();
    }
    
    public function subscribe()
    {
        $this->deferred = new Deferred();
        $this->stream->on('PUBLISH_SEND', Closure::fromCallable([$this, 'onPublish']));
        $this->stream->on('PUBLISH_ACKNOWLEDGE', Closure::fromCallable([$this, 'onResolve']));
        $this->stream->on('PUBLISH_ACKNOWLEDGE_SEND', Closure::fromCallable([$this, 'onResolve']));
        $this->stream->on('PUBLISH_COMPLETE_SEND', Closure::fromCallable([$this, 'onResolve']));

        parent::subscribe();
        
        return $this->deferred->promise();
    }
    
    protected function onPublish($packet)
    {
        if ($packet->qos != 0) {
            return;
        }
        $this->deferred->resolve($this->stream);
    }
    
    protected function onResolve($packet)
    {
        $this->deferred->resolve($this->stream);
    }
}
