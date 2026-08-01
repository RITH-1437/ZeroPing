<?php

/**
 * Authentication configuration.
 *
 * Configures the authentication guards, user providers, and password reset
 * settings used by App\Core\Auth\AuthManager.
 */

declare(strict_types=1);

return [
    'default_guard' => 'web',

    'guards' => [
        'web' => [
            'driver'   => 'session',
            'provider' => 'users',
        ],
        'api' => [
            'driver'   => 'token',
            'provider' => 'users',
        ],
    ],

    'providers' => [
        'users' => [
            'driver' => 'database',
            'table'  => 'users',
        ],
    ],

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table'    => 'password_resets',
            'expire'   => 60,   // minutes
            'throttle' => 60,   // seconds between reset attempts
        ],
    ],

    'password_timeout' => 10800,   // seconds before password confirmation expires (3 hours)
];
