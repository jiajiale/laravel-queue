<?php

return [
    'rabbitmq' => [
        'host' => env('RABBITMQ_HOST', ''),
        'port' => env('RABBITMQ_PORT', ''),
        'username' => env('RABBITMQ_USER', ''),
        'password' => env('RABBITMQ_PASSWORD', ''),
        'vhost' => env('RABBITMQ_VHOST', '/'),
        'qos' => env('RABBITMQ_QOS', 50),
    ]
];