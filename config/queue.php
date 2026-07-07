<?php

return [

    'default' => 'sync',

    'connections' => [

        'sync' => [
            'driver' => 'sync',
        ],

        'database' => [
            'driver' => 'database',
            'table' => 'jobs',
            'queue' => 'default',
            'retry_after' => 90,
        ],

        'array' => [
            'driver' => 'array',
        ],

        'null' => [
            'driver' => 'null',
        ],

    ],

    'failed' => [
        'driver' => 'database',
        'database' => 'mysql',
        'table' => 'failed_jobs',
    ],

];
