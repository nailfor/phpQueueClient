<?php

namespace nailfor\queue\listeners;

use Closure;
use React\Promise\Deferred;

class Acceptor extends Listener
{
    protected $deferred;

    public function subscribe()
    {
        $this->deferred = new Deferred();

        $connectionAckClass = $this->protocol->getConnectionAck();
        $this->stream->on($connectionAckClass::EVENT, Closure::fromCallable([$this, 'onData']));

        return $this->deferred->promise();
    }
    
    protected function onData($packet)
    {
        if ($packet->code) {
            throw new \Exception($packet);
        }

        $this->deferred->resolve($this->stream);
    }
}
