<?php

declare(strict_types=1);

$appKey = getenv('APP_KEY') ?: '';

if ($appKey === '' || $appKey === 'base64:') {
    $isCi = getenv('CI') !== false || getenv('GITHUB_ACTIONS') !== false;
    if (!$isCi) {
        trigger_error('APP_KEY is not set. Run "php zero key:generate" to generate a secure key.', E_USER_WARNING);
    }
}

return [
    'key' => $appKey,
    'hash_driver' => 'bcrypt',
    'rate_limits' => [
        'api' => '60,1',
        'web' => '60,1',
    ],
    'csrf_lifetime' => 120,

    // Never trust X-Forwarded-* headers unless the direct peer is listed.
    // In Docker/Nginx deployments, set TRUSTED_PROXIES to the proxy address.
    'trusted_proxies' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) (getenv('TRUSTED_PROXIES') ?: ''))
    ))),

    /**
     * Security headers configuration
     */
    'headers' => [
        'x_frame_options' => getenv('SECURITY_X_FRAME_OPTIONS') ?: 'SAMEORIGIN',
        'referrer_policy' => getenv('SECURITY_REFERRER_POLICY') ?: 'strict-origin-when-cross-origin',
        'hsts' => getenv('SECURITY_HSTS') ?: 'max-age=31536000; includeSubDomains',
        // Content Security Policy - highly recommended for production
        'csp' => getenv('SECURITY_CSP') ?: null,
        // Example CSP: "default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline';"

        // Permissions Policy (formerly Feature-Policy)
        'permissions_policy' => getenv('SECURITY_PERMISSIONS_POLICY') ?: null,
        // Example: "geolocation=(), microphone=(), camera=()"
    ],

    /**
     * CORS (Cross-Origin Resource Sharing) configuration
     */
    'cors' => [
        'allowed_origins' => array_values(array_filter(array_map(
            'trim',
            explode(',', (string) (getenv('CORS_ALLOWED_ORIGINS') ?: '*'))
        ))),
        'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],
        'allowed_headers' => ['Content-Type', 'Authorization', 'X-Requested-With', 'Accept', 'Origin'],
        'exposed_headers' => [],
        'max_age' => 86400, // 24 hours
        'supports_credentials' => (bool)(getenv('CORS_SUPPORTS_CREDENTIALS') ?: false),
    ],
];
