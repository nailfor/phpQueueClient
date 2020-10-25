<?php

namespace nailfor\queue\listeners;

class Disconnector extends Listener
{
    public function subscribe()
    {
        $protocol = $this->protocol;
        $disconnectClass = $protocol->getDisconnect();
        $packet = new $disconnectClass($this->options, $protocol);

        $this->send($packet);
        
        return $this->stream;
    }
}
