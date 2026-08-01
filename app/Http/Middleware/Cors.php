<?php

declare(strict_types=1);

namespace App\Http\Middleware;

/**
 * CORS (Cross-Origin Resource Sharing) Middleware
 *
 * Handles CORS preflight requests and adds CORS headers to responses,
 * allowing APIs to be consumed from browsers on different domains.
 *
 * Configuration keys (in config/security.php or config/cors.php):
 * - cors.allowed_origins: Array of allowed origins or '*' for all
 * - cors.allowed_methods: Array of allowed HTTP methods
 * - cors.allowed_headers: Array of allowed request headers
 * - cors.exposed_headers: Array of headers to expose to browser
 * - cors.max_age: Cache duration for preflight requests (seconds)
 * - cors.supports_credentials: Whether to allow credentials
 *
 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
 */
class Cors
{
    /**
     * Handle the incoming request and add CORS headers.
     *
     * @param mixed $request The incoming HTTP request
     * @param callable $next The next middleware
     * @return mixed Response with CORS headers
     */
    public function handle(mixed $request, callable $next): mixed
    {
        // Handle OPTIONS preflight request
        if ($this->isPreflightRequest($request)) {
            return $this->handlePreflightRequest($request);
        }

        // Process the actual request
        $response = $next($request);

        // Add CORS headers to response
        return $this->addCorsHeaders($request, $response);
    }

    /**
     * Check if the request is a CORS preflight request.
     *
     * @param mixed $request The request object
     * @return bool True if preflight request
     */
    protected function isPreflightRequest(mixed $request): bool
    {
        $method = $this->getRequestMethod($request);
        $origin = $this->getOriginHeader($request);

        return $method === 'OPTIONS'
            && $origin !== null
            && $this->hasHeader($request, 'HTTP_ACCESS_CONTROL_REQUEST_METHOD');
    }

    /**
     * Handle a CORS preflight request.
     *
     * @param mixed $request The preflight request
     * @return mixed Empty response with CORS headers
     */
    protected function handlePreflightRequest(mixed $request): mixed
    {
        $response = response('', 204);

        // Add CORS headers
        $response = $this->addCorsHeaders($request, $response);

        // Add preflight-specific headers
        $allowedMethods = implode(', ', config('cors.allowed_methods', [
            'GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'
        ]));
        $response->header('Access-Control-Allow-Methods', $allowedMethods);

        $allowedHeaders = implode(', ', config('cors.allowed_headers', [
            'Content-Type', 'Authorization', 'X-Requested-With', 'Accept', 'Origin'
        ]));
        $response->header('Access-Control-Allow-Headers', $allowedHeaders);

        $maxAge = config('cors.max_age', 86400); // 24 hours default
        $response->header('Access-Control-Max-Age', (string)$maxAge);

        return $response;
    }

    /**
     * Add CORS headers to the response.
     *
     * @param mixed $request The request object
     * @param mixed $response The response object
     * @return mixed Response with CORS headers
     */
    protected function addCorsHeaders(mixed $request, mixed $response): mixed
    {
        $origin = $this->getOriginHeader($request);

        if ($origin === null) {
            return $response; // Not a CORS request
        }

        // Check if origin is allowed
        $allowedOrigin = $this->getAllowedOrigin($origin);
        if ($allowedOrigin === null) {
            return $response; // Origin not allowed
        }

        $response->header('Access-Control-Allow-Origin', $allowedOrigin);
        $response->header('Vary', 'Origin');

        // Allow credentials if configured
        if (config('cors.supports_credentials', false)) {
            $response->header('Access-Control-Allow-Credentials', 'true');
        }

        // Expose additional headers if configured
        $exposedHeaders = config('cors.exposed_headers', []);
        if (!empty($exposedHeaders)) {
            $response->header('Access-Control-Expose-Headers', implode(', ', $exposedHeaders));
        }

        return $response;
    }

    /**
     * Get the allowed origin for the given request origin.
     *
     * @param string $origin The request origin
     * @return string|null The allowed origin or null if not allowed
     */
    protected function getAllowedOrigin(string $origin): ?string
    {
        $allowedOrigins = config('cors.allowed_origins', ['*']);

        // Allow all origins
        if (in_array('*', $allowedOrigins, true)) {
            return '*';
        }

        // Check if origin is in allowed list
        if (in_array($origin, $allowedOrigins, true)) {
            return $origin;
        }

        // Check wildcard patterns (e.g., *.example.com)
        foreach ($allowedOrigins as $allowedOrigin) {
            if ($this->matchesPattern($origin, $allowedOrigin)) {
                return $origin;
            }
        }

        return null;
    }

    /**
     * Check if origin matches a wildcard pattern.
     *
     * @param string $origin The origin to check
     * @param string $pattern The pattern (e.g., *.example.com)
     * @return bool True if matches
     */
    protected function matchesPattern(string $origin, string $pattern): bool
    {
        if (strpos($pattern, '*') === false) {
            return false;
        }

        $pattern = str_replace('*', '.*', preg_quote($pattern, '/'));
        return (bool)preg_match('/^' . $pattern . '$/', $origin);
    }

    /**
     * Get the Origin header from the request.
     *
     * @param mixed $request The request object
     * @return string|null The origin or null
     */
    protected function getOriginHeader(mixed $request): ?string
    {
        if (method_exists($request, 'header')) {
            return $request->header('Origin');
        }

        return $_SERVER['HTTP_ORIGIN'] ?? null;
    }

    /**
     * Get the request method.
     *
     * @param mixed $request The request object
     * @return string The HTTP method
     */
    protected function getRequestMethod(mixed $request): string
    {
        if (method_exists($request, 'method')) {
            return strtoupper($request->method());
        }

        return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    }

    /**
     * Check if a header exists in the request.
     *
     * @param mixed $request The request object
     * @param string $header The header name (in $_SERVER format)
     * @return bool True if header exists
     */
    protected function hasHeader(mixed $request, string $header): bool
    {
        return isset($_SERVER[$header]);
    }
}
