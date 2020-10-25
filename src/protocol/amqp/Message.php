<?php

namespace nailfor\queue\protocol\amqp;

class Message extends Packet 
{
    const EVENT = 'MESSAGE';
    
    protected $fillable = [
        'expires',
        'destination',
        'msgOpCode',
        'subscription',
        'priority',
        'type',
        'affectedResources',
        'event_type',
        'message-id',
        'timestamp',
        'federator',
    ];
    
    protected $json = [
        'expires',
        'priority',
        'affectedResources',
        'timestamp',
        'federator',
    ];
    protected $body;
    

    public function __construct($options) 
    {
        $data = [];
        foreach ($options as $key=>$option) {
            if (!$option) {
                $v = trim($options[$key+1] ?? '');
                $this->body = json_decode($v);
                break;
            }
            
            $pos = strpos($option, ':');
            if ($pos !== false) {
                $k = substr($option, 0, $pos);
                $v = substr($option, $pos+1);
                
                if ($k && $v) {
                    if (in_array($k, $this->json)) {
                        $v = json_decode($v);
                    }
                    $data[$k] = $v;
                }
                
            }            
            
        }
        $this->fill($data);
    }
    
}
