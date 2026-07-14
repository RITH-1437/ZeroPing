<?php

return [

    'default' => 'log',

    'mailers' => [

        'smtp' => [
            'transport' => 'smtp',
            'host' => 'localhost',
            'port' => 2525,
            'encryption' => null,
            'username' => null,
            'password' => null,
            'timeout' => null,
            'auth_mode' => null,
        ],

        'log' => [
            'transport' => 'log',
            'channel' => 'mail',
        ],

        'array' => [
            'transport' => 'array',
        ],

        'null' => [
            'transport' => 'null',
        ],

    ],

    'from' => [
        'address' => 'hello@example.com',
        'name' => 'Example',
    ],

    'reply_to' => [
        'address' => 'hello@example.com',
        'name' => 'Example',
    ],

];
