<?php

namespace App\Core\Security\Middleware;

use App\Core\Http\Request;
use App\Core\Security\RateLimiter;
use App\Core\Security\Exceptions\SecurityException;

class ThrottleRequests
{
    public function handle(\Closure $next, int $maxAttempts = 60, int $decayMinutes = 1)
    {
        $key = $this->resolveRequestSignature();

        if (! RateLimiter::attempt($key, $maxAttempts, $decayMinutes)) {
            throw new SecurityException('Too Many Attempts.');
        }

        return $next(new Request());
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
