<?php

namespace nailfor\queue\listeners;

use React\Promise\Deferred;

class Unsubscriber extends Subscriber
{
    protected $deferred;
    
    protected function getPacketClass() 
    {
        return $this->protocol->getUnsubscribe();
    }
    
    public function subscribe()
    {
        $this->deferred = new Deferred();
        $this->stream->on('UNSUBSCRIBE_ACKNOWLEDGE', Closure::fromCallable([$this, 'onData']));

        parent::subscribe();
        
        return $this->deferred->promise();
    }
    
    protected function onData($packet)
    {
        $this->deferred->resolve($this->stream);
    }    
}
