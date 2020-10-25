<?php

namespace nailfor\queue\listeners;

class Pinger extends Subscriber
{
    protected function getPacketClass() 
    {
        return $this->protocol->getPing();
    }
}
