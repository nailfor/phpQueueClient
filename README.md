# PHP MQTT Client

phpQueueClient is an client library for AMQP and MQTT queues server. Its based on the reactPHP socket-client

## Goal

Goal of this project is easy to use both AMQP and MQTT client for PHP in a modern architecture without using any php modules.
* Protocol specifications: http://docs.oasis-open.org/mqtt/mqtt/v3.1.1/csprd02/mqtt-v3.1.1-csprd02.html

## Example publish

```php
```

## Example subscribe

```php

use nailfor\queue\ClientFactory;

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

        ClientFactory::run($url, $options, [$this, 'onError']);
    }
}
```

## Notice - (Oct 21th, 2020)
Currently works:
* AMQP implementation
* subscribe
* publish.. in work
