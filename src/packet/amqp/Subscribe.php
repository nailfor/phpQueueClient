<?php

namespace nailfor\queue\packet\amqp;

class Subscribe extends Packet 
{
    static $subId = 0;
    protected $command = 'SUBSCRIBE';
    
    protected $fillable = [
        'destination',
        'ack',
    ];
    

    public function __construct($topic) 
    {
        $this->id = static::$subId;
        $this->ack = $this->attributes['ack'] ?? 'auto';
        $this->destination = $topic;
        
        static::$subId++;
        
        parent::__construct();
    }
    
}
