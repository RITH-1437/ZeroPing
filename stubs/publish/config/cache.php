<?php

return [

    'default' => 'file',

    'stores' => [

        'file' => [
            'driver' => 'file',
            'path' => BASE_PATH . '/storage/cache',
        ],

        'array' => [
            'driver' => 'array',
        ],

        'null' => [
            'driver' => 'null',
        ],

    ],

    'lifetime' => 3600,

];
