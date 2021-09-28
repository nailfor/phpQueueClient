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
        
        return $this->stream;
    }
    
    protected function onData($rawData)
    {
        $raw = bin2hex($rawData);
        $this->log("raw data: '$raw'", 'debug');
        try {
            foreach ($this->protocol->next($rawData) as $packet) {
                if (!$packet) {
                    $this->log("recieve '$raw'", 'error');
                    $this->stream->emit('INVALID', [$rawData]);
                    
                    continue;
                }
                $class = $packet->getName();
                $event = $packet->getEventName();

                $this->log("recieve '$class', emit '$event'");
                $this->log("$class: $packet", 'info');
                $this->stream->emit($event, [$packet]);
                
                if (method_exists($packet, 'getTopic')) {
                    $topic = $packet->getTopic();
                    $this->stream->emit($topic, [$packet]);
                }
            }
        }
        catch (Violation $e) {
            $raw = bin2hex($rawData);

            $this->log("recieve: '$raw'", 'error');
            $this->stream->emit('INVALID', [$rawData]);
        }
    }
}
