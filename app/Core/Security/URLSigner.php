<?php

declare(strict_types=1);

namespace App\Core\Security;

/**
 * URL signing utility for named routes.
 *
 * Builds signed URLs from named routes, supporting both permanent
 * and time-limited (temporary) signatures for secure URL distribution.
 */
class URLSigner
{
    /**
     * Generate a signed URL for a named route.
     *
     * The resulting URL includes an HMAC signature that prevents tampering.
     *
     * @param string               $name       The route name.
     * @param array<string, mixed> $parameters Route parameters.
     * @return string The fully signed URL.
     */
    public static function signedRoute(string $name, array $parameters = []): string
    {
        $url = route($name, $parameters);

        return Signature::sign($url);
    }

    /**
     * Generate a temporary signed URL for a named route with an expiration time.
     *
     * The URL will include an expiration timestamp parameter in addition to
     * the HMAC signature, allowing the receiving end to reject expired URLs.
     *
     * @param string               $name       The route name.
     * @param int                  $expiration Expiration time as a Unix timestamp.
     * @param array<string, mixed> $parameters Route parameters.
     * @return string The signed URL with expiration.
     */
    public static function temporarySignedRoute(string $name, int $expiration, array $parameters = []): string
    {
        $url = route($name, $parameters, true, $expiration);

        return Signature::sign($url);
    }
}
