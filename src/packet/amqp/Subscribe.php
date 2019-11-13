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
    

    public function __construct($options) 
    {
        $this->fill($options);
        $this->attributes['id'] = static::$subId;
        $this->attributes['ack'] = $this->attributes['ack'] ?? 'auto';
        static::$subId++;
        
        parent::__construct();
    }
    
}
