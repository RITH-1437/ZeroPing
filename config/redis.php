<?php

/**
 * Redis configuration.
 *
 * Configures Redis connections used by the cache, queue, and session systems.
 * Requires the ext-redis PHP extension.
 */

declare(strict_types=1);

return [
    'client' => getenv('REDIS_CLIENT') ?: 'phpredis',

    'default' => [
        'host' => getenv('REDIS_HOST') ?: '127.0.0.1',
        'password' => getenv('REDIS_PASSWORD') ?: null,
        'port' => (int)(getenv('REDIS_PORT') ?: 6379),
        'database' => (int)(getenv('REDIS_DB') ?: 0),
    ],

    'cache' => [
        'host' => getenv('REDIS_HOST') ?: '127.0.0.1',
        'password' => getenv('REDIS_PASSWORD') ?: null,
        'port' => (int)(getenv('REDIS_PORT') ?: 6379),
        'database' => (int)(getenv('REDIS_CACHE_DB') ?: 1),
    ],
];