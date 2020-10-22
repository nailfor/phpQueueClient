<?php

namespace nailfor\queue\listeners;

use Closure;
use nailfor\queue\protocol\Violation;
use nailfor\queue\packet\ControlPacket;

class Listener
{
    protected $stream;
    protected $protocol;
    protected $Log;
    protected $options;

    public function __construct($stream, $protocol, $options, $log = null) 
    {
        $this->stream = $stream;
        $this->protocol = $protocol;
        $this->Log = $log;
        $this->options = $options;
    }

    protected function sendPacketToStream(ControlPacket $controlPacket)
    {
        $this->log('send '. $controlPacket);
        
        $message = $controlPacket->get();
        return $this->stream->write($message);
    }
    
    protected function log($str)
    {
        if ($this->Log) {
            $this->Log->info($str);
        }
    }
}
