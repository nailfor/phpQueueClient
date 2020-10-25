<?php

namespace nailfor\queue\listeners;

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
    
    /**
     * Set listener on event
     * @param type $params
     */
    public function setEvent($params)
    {
        $events = $params['events'] ?? [];
        
        foreach ($events as $event => $callback) {
            $this->stream->on($event, $callback);
        }
    }    

    /**
     * Send packet to stream
     * @param ControlPacket $controlPacket
     * @return type
     */
    protected function send(ControlPacket $controlPacket)
    {
        $class = $controlPacket->getName();
        

        $message = $controlPacket->get();
        $payload = bin2hex($message);

        $controlPacket->incrasePacket();
        $res = $this->stream->write($message);
        
        $class = $controlPacket->getName();
        $event = $controlPacket->getEventName() . '_SEND';

        $this->stream->emit($event, [$controlPacket]);
        
        $this->log("send '$class', emit '$event'");
        $this->log("$class: $controlPacket", "info");
        $this->log("raw data: '$payload'", 'debug');
        
        return $res;
    }
    
    /**
     * Logger
     * @param string $str
     * @param string $level
     */
    protected function log($str, $level='notice')
    {
        if ($this->Log) {
            $this->Log->$level($str);
        }
    }
}
