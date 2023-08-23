<?php
return [
    'redis' => [
        'cluster' => false,
        'client' => 'predis',
        'default' => [
            'host'     => env('REDIS_HOST', '127.0.0.1'),
            'port'     => env('REDIS_PORT', 6379),
            'password' => env('REDIS_PASSWORD', null),
            'database' => env('REDIS_DATABASE', 0),
        ],
    ]
];
