<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection
    |--------------------------------------------------------------------------
    |
    | The connection used by default when no connection name is given. The
    | value is read from the DB_CONNECTION environment variable and falls
    | back to SQLite so a brand-new project works with zero configuration.
    |
    */

    'default' => env('DB_CONNECTION', 'sqlite'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Each connection is keyed by a name and selects a driver. The ORM only
    | ever talks to the DatabaseManager, so switching engines is a pure
    | configuration change — no application code has to change.
    |
    */

    'connections' => [

        'sqlite' => [
            'driver'   => 'sqlite',
            'database' => env('DB_DATABASE', database_path('database.sqlite')),
        ],

        'mysql' => [
            'driver'   => 'mysql',
            'host'     => env('DB_HOST', '127.0.0.1'),
            'port'     => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', ''),
            'username' => env('DB_USERNAME', env('DB_USER', '')),
            'password' => env('DB_PASSWORD', env('DB_PASS', '')),
            'charset'  => env('DB_CHARSET', 'utf8mb4'),
            'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'),
        ],

        'mariadb' => [
            'driver'   => 'mariadb',
            'host'     => env('DB_HOST', '127.0.0.1'),
            'port'     => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', ''),
            'username' => env('DB_USERNAME', env('DB_USER', '')),
            'password' => env('DB_PASSWORD', env('DB_PASS', '')),
            'charset'  => env('DB_CHARSET', 'utf8mb4'),
            'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'),
        ],

        'pgsql' => [
            'driver'   => 'pgsql',
            'host'     => env('DB_HOST', '127.0.0.1'),
            'port'     => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', ''),
            'username' => env('DB_USERNAME', env('DB_USER', '')),
            'password' => env('DB_PASSWORD', env('DB_PASS', '')),
            'charset'  => env('DB_CHARSET', 'utf8'),
            'schema'   => env('DB_SCHEMA', 'public'),
        ],

    ],

];
