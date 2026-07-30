<?php

declare(strict_types=1);

namespace App\Core\Security\Middleware;

use App\Core\Http\Request;
use App\Core\Security\RateLimiter;
use App\Core\Security\Exceptions\SecurityException;

/**
 * Middleware that throttles incoming requests based on rate limiting.
 *
 * Identifies requests by a combination of HTTP method, path, and client IP.
 * When the maximum number of attempts is exceeded within the decay window,
 * a SecurityException is thrown with a 429-style error.
 */
class ThrottleRequests
{
    /**
     * Handle the throttle check for the current request.
     *
     * If the request exceeds the maximum allowed attempts within the
     * specified decay window, a SecurityException is thrown.
     *
     * @param int $maxAttempts  The maximum number of requests allowed within the window.
     * @param int $decayMinutes The time window in minutes before the counter resets.
     * @return void
     *
     * @throws SecurityException When the rate limit has been exceeded.
     */
    public function handle(int $maxAttempts = 60, int $decayMinutes = 1): void
    {
        $key = $this->resolveRequestSignature();

        if (!RateLimiter::attempt($key, $maxAttempts, $decayMinutes)) {
            $retryAfter = $decayMinutes * 60;

            throw new SecurityException(
                "Too Many Attempts. Retry after {$retryAfter} seconds."
            );
        }
    }

    /**
     * Resolve a unique signature for the current request.
     *
     * Combines the HTTP method, request path, and client IP into a
     * SHA-1 hash to create a unique rate limiter key.
     *
     * @return string The request signature hash.
     */
    protected function resolveRequestSignature(): string
    {
        return sha1(
            Request::method() . '|' . Request::path() . '|' . Request::ip()
        );
    }
}
