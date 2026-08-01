<?php

declare(strict_types=1);

namespace App\Http\Middleware;

/**
 * Security Headers Middleware
 *
 * Adds security-related HTTP headers to all responses to protect against
 * common web vulnerabilities (XSS, clickjacking, MIME sniffing, etc.).
 *
 * Headers added:
 * - X-Frame-Options: Prevents clickjacking attacks
 * - X-Content-Type-Options: Prevents MIME type sniffing
 * - X-XSS-Protection: Enables browser XSS protection
 * - Referrer-Policy: Controls referrer information
 * - Strict-Transport-Security: Enforces HTTPS (when on HTTPS)
 * - Content-Security-Policy: Restricts resource loading (configurable)
 *
 * @see https://owasp.org/www-project-secure-headers/
 */
class SecurityHeaders
{
    /**
     * Handle the incoming request and add security headers to response.
     *
     * @param mixed $request The incoming HTTP request
     * @param callable $next The next middleware
     * @return mixed Response with security headers
     */
    public function handle(mixed $request, callable $next): mixed
    {
        $response = $next($request);

        // Prevent clickjacking by restricting iframe embedding
        $response->header('X-Frame-Options', config('security.headers.x_frame_options', 'SAMEORIGIN'));

        // Prevent MIME type sniffing
        $response->header('X-Content-Type-Options', 'nosniff');

        // Enable browser's built-in XSS protection
        $response->header('X-XSS-Protection', '1; mode=block');

        // Control referrer information sent with requests
        $response->header('Referrer-Policy', config('security.headers.referrer_policy', 'strict-origin-when-cross-origin'));

        // Enforce HTTPS connections (only when already on HTTPS)
        if ($this->isSecureRequest($request)) {
            $hsts = config('security.headers.hsts', 'max-age=31536000; includeSubDomains');
            $response->header('Strict-Transport-Security', $hsts);
        }

        // Content Security Policy (if configured)
        if ($csp = config('security.headers.csp')) {
            $response->header('Content-Security-Policy', $csp);
        }

        // Permissions Policy (formerly Feature-Policy)
        if ($permissionsPolicy = config('security.headers.permissions_policy')) {
            $response->header('Permissions-Policy', $permissionsPolicy);
        }

        return $response;
    }

    /**
     * Check if the request is secure (HTTPS).
     *
     * @param mixed $request The request object
     * @return bool True if HTTPS, false otherwise
     */
    protected function isSecureRequest(mixed $request): bool
    {
        // Check if request object has isSecure method
        if (method_exists($request, 'isSecure')) {
            return $request->isSecure();
        }

        // Fallback: check $_SERVER variables
        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)
            || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
    }
}
