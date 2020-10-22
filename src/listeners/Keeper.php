<?php

namespace nailfor\queue\listeners;

use Closure;
use React\Promise\FulfilledPromise;

class Keeper extends Listener
{
    public function subscribe()
    {
        $options = $this->options;
        $keepAlive = $options->keepAlive;
        if($keepAlive > 0) {
            $interval = $keepAlive / 2;

            $this->loop->addPeriodicTimer($interval, Closure::fromCallable([$this, 'onData']));
        }

        return new FulfilledPromise($stream);
    }
    
    protected function onData($rawData)
    {
        $pingClass = $this->protocol->getPing();
        
        $packet = new $pingClass;
        $this->sendPacketToStream($packet);
    }
}
