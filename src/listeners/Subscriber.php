<?php

namespace nailfor\queue\listeners;

use Closure;
use React\Promise\Deferred;

class Subscriber extends Listener
{
    protected $deferred;

    public function subscribe()
    {
        $topics = $this->options->topics ?? [];

        $subscribeClass = $this->protocol->getSubscribe();
        foreach ($topics as $topic => $params) {
            $subscribe = new $subscribeClass($topic);

            $this->sendPacketToStream($subscribe);
            $this->protocol->setEvent($this->stream, $params);
        }
    }
}
