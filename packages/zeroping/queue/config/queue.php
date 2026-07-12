<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default queue connection
    |--------------------------------------------------------------------------
    */

    'default' => env('QUEUE_CONNECTION', 'sync'),

    /*
    |--------------------------------------------------------------------------
    | Queue connections
    |--------------------------------------------------------------------------
    |
    | Each connection names a "driver". The QueueManager::resolve() hook maps
    | the driver to a concrete Queue implementation (database, redis, ...).
    |
    */

    'connections' => [
        'sync' => [
            'driver' => 'sync',
        ],

        // 'database' => [
        //     'driver'  => 'database',
        //     'table'   => 'jobs',
        // ],
        //
        // 'redis' => [
        //     'driver' => 'redis',
        //     'host'   => env('REDIS_HOST', '127.0.0.1'),
        // ],
    ],
];
