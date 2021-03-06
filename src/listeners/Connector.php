<?php

namespace nailfor\queue\listeners;

class Connector extends Listener
{
    public function subscribe()
    {
        $protocol = $this->protocol;
        $connectClass = $protocol->getConnect();
        $packet = new $connectClass($this->options, $protocol);

        $this->send($packet);
        
        return $this->stream;
    }
}
