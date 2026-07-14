<?php

return [

    'default' => 'local',

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => BASE_PATH . '/storage',
        ],

        'public' => [
            'driver' => 'local',
            'root' => BASE_PATH . '/storage/public',
            'url' => '/storage',
        ],

        'cache' => [
            'driver' => 'local',
            'root' => BASE_PATH . '/storage/cache',
        ],

    ],

];
