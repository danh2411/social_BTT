<?php

return [
    'default' => env('ELASTICSEARCH_CONNECTION', 'default'),
    'allowed_elasticsearch' => env('ALLOWED_ELASTICSEARCH', true),
    'connections' => [
        'default' => [
            'host' => env('ELASTICSEARCH_HOST', '127.0.0.1'),
            'port' => env('ELASTICSEARCH_PORT', '9200'),
            'username' => env('ELASTICSEARCH_USERNAME', ''),
            'password' => env('ELASTICSEARCH_PASSWORD', ''),
            'prefix' => env('ELASTICSEARCH_PREFIX', ''),
        ]
    ]
];
