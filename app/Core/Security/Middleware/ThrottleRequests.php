<?php

declare(strict_types=1);

namespace App\Core\Security\Middleware;

use App\Core\Http\Request;
use App\Core\Security\RateLimiter;
use App\Core\Security\Exceptions\SecurityException;

class ThrottleRequests
{
    /**
     * Handle the throttle check.
     *
     * If the request exceeds the maximum attempts, a SecurityException is thrown.
     *
     * @param int $maxAttempts
     * @param int $decayMinutes
     *
     * @throws SecurityException When the rate limit has been exceeded.
     */
    public function handle(int $maxAttempts = 60, int $decayMinutes = 1): void
    {
        $key = $this->resolveRequestSignature();

        if (!RateLimiter::attempt($key, $maxAttempts, $decayMinutes)) {
            throw new SecurityException('Too Many Attempts.');
        }
    }

    protected function resolveRequestSignature(): string
    {
        return sha1(
            Request::method() .
            '|' . Request::url() .
            '|' . Request::ip()
        );
    }
}
