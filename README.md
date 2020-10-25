# PHP MQTT Client

phpQueueClient is an client library for AMQP and MQTT queues server. Its based on the reactPHP socket-client

## Goal

Goal of this project is easy to use both AMQP and MQTT client for PHP in a modern architecture without using any php modules.
Original idea taken from https://github.com/oliverlorenz/phpMqttClient

* yes it correctly supports both qos protocols
* yes it works with huge data in a packet
* yes it can receive, ping, unsubscribe and publish

* Protocol specifications: http://docs.oasis-open.org/mqtt/mqtt/v3.1.1/csprd02/mqtt-v3.1.1-csprd02.html

## Example publish

```php
use nailfor\queue\ClientFactory;
use nailfor\queue\protocol\MQTT;

class MQTTClass 
{
    public static function subscribe()
    {
        $url    = '127.0.0.1:5672';

        $options = [
            //'username'  => '',
            //'password'  => '',
            'clientId'  => 'php',
            'cleanSession' => false,

            'topics'    => [
                'topic_name' => [
                    //this flag clear message after reciev. Default true
                    //'clear'     => false,
                    'qos'       => 1, //only for MQTT
                    'message'    => 'hello from topic',
                ],
                'capital_name' => [
                    'qos'       => 2, //only for MQTT
                    'message'    => 'hello from capital',
                ],
            ],        
        ];

        $protocol = new MQTT; //default AMQP
        ClientFactory::publish($url, $options, [$this, 'onError'], $protocol);
    }
}
```

## Example subscribe

```php

use nailfor\queue\ClientFactory;
use nailfor\queue\protocol\MQTT;
use Illuminate\Support\Facades\Log;

class MQTTClass 
{
    public function onMessage($packet)
    {
        $id = $packet->getMessageId();
        $payload = $packet->getPayload();
    }

    public function onCapitalMessage($packet)
    {
        $id = $packet->getMessageId();
        $payload = $packet->getPayload();
    }

    public function onError($reason) {
        echo $reason->getMessage(). PHP_EOL;
        exit;
    }

    public static function subscribe()
    {
        $url    = '127.0.0.1:5672';

        $options = [
            //'username'  => '',
            //'password'  => '',
            'clientId'  => 'php',
            'cleanSession' => false,

            'topics'    => [
                'topic_name' => [
                    //this flag clear message after reciev. Default true
                    //'clear'     => false,
                    'qos'       => 1, //only for MQTT
                    'events'    => [
                        'PUBLISH' => [$this, 'onMessage'],
                    ],
                ],
                'capital_name' => [
                    'qos'       => 0, //only for MQTT
                    'events'    => [
                        'PUBLISH' => [$this, 'onCapitalMessage'],
                    ],
                ],
            ],        
        ];

        $protocol = new MQTT; //default AMQP
        $logger = Log::channel('stderr'); //default null
        ClientFactory::run($url, $options, [$this, 'onError'], $protocol, $logger);
    }
}
```

also commands are available
    ClientFactory::run //subscribe and get messages
    ClientFactory::publish
    ClientFactory::unsubscribe
    ClientFactory::ping


## Notice - (Oct 21th, 2020)
Currently work:
* AMQP implementation:
* publish.. not yet
* subscribe
* unsubscribe

* MQTT implementation:
*  - qos 1
*  - qos 2
* subscribe
* unsubscribe
* publish
