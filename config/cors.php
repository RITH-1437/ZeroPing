<?php

/**
 * CORS (Cross-Origin Resource Sharing) configuration.
 *
 * Controls which origins, methods, and headers are permitted for cross-origin
 * requests. These settings are consumed by App\Http\Middleware\Cors.
 *
 * @see App\Http\Middleware\Cors
 */

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Allowed Origins
    |--------------------------------------------------------------------------
    | List of origins that may make cross-origin requests.
    | Use '*' to allow all origins (not recommended for production APIs).
    | Use specific origins: ['https://example.com', 'https://app.example.com']
    | Wildcard subdomains are supported: '*.example.com'
    */
    'allowed_origins' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) (getenv('CORS_ALLOWED_ORIGINS') ?: '*'))
    ))),

    /*
    |--------------------------------------------------------------------------
    | Allowed Methods
    |--------------------------------------------------------------------------
    */
    'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],

    /*
    |--------------------------------------------------------------------------
    | Allowed Headers
    |--------------------------------------------------------------------------
    */
    'allowed_headers' => [
        'Content-Type',
        'Authorization',
        'X-Requested-With',
        'Accept',
        'Origin',
        'X-CSRF-TOKEN',
    ],

    /*
    |--------------------------------------------------------------------------
    | Exposed Headers
    |--------------------------------------------------------------------------
    | Headers that may be exposed to browser JavaScript.
    */
    'exposed_headers' => [],

    /*
    |--------------------------------------------------------------------------
    | Max Age
    |--------------------------------------------------------------------------
    | Preflight request cache duration in seconds (default: 24 hours).
    */
    'max_age' => 86400,

    /*
    |--------------------------------------------------------------------------
    | Supports Credentials
    |--------------------------------------------------------------------------
    | Set to true only when cookies/credentials must be sent cross-origin.
    | Cannot be used alongside allowed_origins = ['*'].
    */
    'supports_credentials' => (bool) (getenv('CORS_SUPPORTS_CREDENTIALS') ?: false),
];
