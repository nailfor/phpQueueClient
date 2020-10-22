<?php

namespace nailfor\queue\listeners;

use Closure;
use React\Promise\Deferred;

class Connector extends Listener
{
    protected $deferred;

    public function subscribe()
    {
        $options = $this->options;
        $connectClass = $this->protocol->getConnect();
        $packet = new $connectClass($options);

        $this->deferred = new Deferred();
        
        $connectionAckClass = $this->protocol->getConnectionAck();
        $this->stream->on($connectionAckClass::EVENT, Closure::fromCallable([$this, 'onData']));

        $this->sendPacketToStream($packet);

        return $this->deferred->promise();
    }
    
    protected function onData($rawData)
    {
        $this->deferred->resolve($this->stream);
    }
}
