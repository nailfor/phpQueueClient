<?php

namespace nailfor\queue\listeners;

class Subscriber extends Listener
{
    protected function getPacketClass() 
    {
        return $this->protocol->getSubscribe();
    }
    
    public function subscribe()
    {
        $topics = $this->options->topics ?? [];

        $packetClass = $this->getPacketClass();
        foreach ($topics as $topic => $params) {
            $params['topic'] = $topic;
            $packet = new $packetClass($params, $this->protocol);
            
            $this->send($packet);
            $this->setEvent($params);
        }
        
        return $this->stream;
    }
}
