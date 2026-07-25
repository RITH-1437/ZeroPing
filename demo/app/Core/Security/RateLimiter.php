<?php

namespace App\Core\Security;

use App\Core\Cache\Cache;

class RateLimiter
{
    public static function attempt(string $key, int $maxAttempts, int $decayMinutes = 1): bool
    {
        $key = 'rate-limiter:' . $key;

        if (Cache::has($key)) {
            $attempts = Cache::get($key);
            if ($attempts >= $maxAttempts) {
                return false;
            }
            Cache::increment($key);
        } else {
            Cache::put($key, 1, $decayMinutes * 60);
        }

        return true;
    }
}
