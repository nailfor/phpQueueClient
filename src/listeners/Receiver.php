<?php

namespace nailfor\queue\listeners;

use nailfor\queue\protocol\Violation;
use nailfor\queue\packet\ControlPacket;

use Closure;

class Receiver extends Listener
{
    public function subscribe()
    {
        $this->stream->on('data', Closure::fromCallable([$this, 'onData']));
    }
    
    protected function onData($rawData)
    {
        $this->log("recieve: $rawData");
        try {
            foreach ($this->protocol->next($rawData) as $packet) {
                $event = $packet::EVENT;
                $this->stream->emit($event, [$packet]);
            }
        }
        catch (Violation $e) {
            $this->stream->emit('INVALID', [$e]);
        }
    }
}
