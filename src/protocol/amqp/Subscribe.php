<?php

namespace nailfor\queue\protocol\amqp;

class Subscribe extends Packet 
{
    const EVENT = 'SUBSCRIBE';
    static $subId = 0;
    protected $command = 'SUBSCRIBE';
    
    protected $fillable = [
        'destination',
        'ack',
    ];
    

    public function __construct($params) 
    {
        $this->id = static::$subId;
        $this->ack = $params['ack'] ?? 'auto';
        $this->destination = $params['topic'] ?? '';
        
        static::$subId++;
        
        parent::__construct();
    }
    
}
