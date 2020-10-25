<?php

namespace nailfor\queue\protocol\amqp;

class Connect extends Packet 
{
    const EVENT = 'CONNECT';
    protected $command = 'CONNECT';

    protected $fillable = [
        'username',
        'passcode',
    ];
    
    public function __construct($options) 
    {
        $this->fill($options);
        $pass = $options->password ?? '';
        if ($pass) {
            $this->attributes['passcode'] = $pass;
        }
        
        parent::__construct();
    }
    
    /**
     * @return string
     */
    public function getClientId()
    {
        if (is_null($this->clientId)) {
            $this->clientId = md5(microtime());
        }
        return substr($this->clientId, 0, 23);
    }
}
