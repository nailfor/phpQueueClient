<?php
namespace nailfor\queue\tests;

use nailfor\queue\ClientFactory;
use Illuminate\Support\Facades\Log;

class ConnectionTest
{
    public static function message($message)
    {
        echo json_encode($message);
    }

    public static function listen()
    {
        $url = config('amqp.url');
        $resolver = config('amqp.resolver'); //8.8.8.8 or null
        
        $options = [
            //'username'  => '',
            //'password'  => '',

            'topics'    => [
                env('AMQP_TOPIC', 'topic') => [
                    'events' => [
                        'MESSAGE' => [static::class, 'message'],
                    ],
                ],
            ],
        ];

        $logger = Log::channel('stderr');
        ClientFactory::run($url, $options, null, null, null, $logger);

    }

}
