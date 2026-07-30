<?php

declare(strict_types=1);

namespace App\Core\Security;

use App\Core\Cache\Cache;

/**
 * Flexible rate limiter supporting custom storage backends.
 *
 * By default uses the framework's Cache system, but can be configured
 * with any storage implementation via the RateLimiterStorageInterface.
 * This allows Redis, Memcached, database, or in-memory rate limiting.
 *
 * Usage with default cache storage:
 *   RateLimiter::attempt('login:' . $ip, 5, 1);
 *
 * Usage with custom storage:
 *   $limiter = new RateLimiter(new RedisRateLimiterStorage($redis));
 *   $limiter->attemptOn('login:' . $ip, 5, 1);
 */
class RateLimiter
{
    /** @var RateLimiterStorageInterface|null Custom storage backend. */
    private ?RateLimiterStorageInterface $storage;

    /**
     * Create a new RateLimiter instance.
     *
     * @param RateLimiterStorageInterface|null $storage Custom storage backend; uses Cache if null.
     */
    public function __construct(?RateLimiterStorageInterface $storage = null)
    {
        $this->storage = $storage;
    }

    /**
     * Attempt an action using the default cache-backed storage.
     *
     * Returns true if the attempt is allowed (under the limit), false if rate-limited.
     *
     * @param string $key          Unique key identifying the rate-limited action.
     * @param int    $maxAttempts  Maximum number of attempts allowed within the decay window.
     * @param int    $decayMinutes The time window in minutes before attempts reset.
     * @return bool True if the attempt is allowed.
     */
    public static function attempt(string $key, int $maxAttempts, int $decayMinutes = 1): bool
    {
        $prefixedKey = 'rate-limiter:' . $key;

        if (Cache::has($prefixedKey)) {
            $attempts = (int) Cache::get($prefixedKey);
            if ($attempts >= $maxAttempts) {
                return false;
            }
            Cache::increment($prefixedKey);
        } else {
            Cache::put($prefixedKey, 1, $decayMinutes * 60);
        }

        return true;
    }

    /**
     * Attempt an action using the configured custom storage backend.
     *
     * Provides the same rate limiting logic but backed by the injected storage.
     *
     * @param string $key          Unique key identifying the rate-limited action.
     * @param int    $maxAttempts  Maximum number of attempts allowed within the decay window.
     * @param int    $decaySeconds The time window in seconds before attempts reset.
     * @return bool True if the attempt is allowed.
     *
     * @throws \RuntimeException If no custom storage has been configured.
     */
    public function attemptOn(string $key, int $maxAttempts, int $decaySeconds = 60): bool
    {
        if ($this->storage === null) {
            throw new \RuntimeException('No custom storage configured. Use attempt() for cache-backed rate limiting.');
        }

        $prefixedKey = 'rate-limiter:' . $key;
        $current = $this->storage->get($prefixedKey);

        if ($current !== null) {
            if ($current >= $maxAttempts) {
                return false;
            }
            $this->storage->increment($prefixedKey);
        } else {
            $this->storage->set($prefixedKey, 1, $decaySeconds);
        }

        return true;
    }

    /**
     * Get the number of remaining attempts for a given key (static/cache-backed).
     *
     * @param string $key         Unique key identifying the rate-limited action.
     * @param int    $maxAttempts The maximum attempts allowed.
     * @return int The number of remaining attempts.
     */
    public static function remaining(string $key, int $maxAttempts): int
    {
        $prefixedKey = 'rate-limiter:' . $key;
        $attempts = (int) Cache::get($prefixedKey, 0);

        return max(0, $maxAttempts - $attempts);
    }

    /**
     * Reset the rate limiter for a given key (static/cache-backed).
     *
     * @param string $key The key to reset.
     * @return void
     */
    public static function reset(string $key): void
    {
        Cache::forget('rate-limiter:' . $key);
    }

    /**
     * Determine if the given key has been rate-limited (static/cache-backed).
     *
     * @param string $key         Unique key identifying the rate-limited action.
     * @param int    $maxAttempts The maximum attempts allowed.
     * @return bool True if the key is currently rate-limited.
     */
    public static function tooManyAttempts(string $key, int $maxAttempts): bool
    {
        $prefixedKey = 'rate-limiter:' . $key;

        if (!Cache::has($prefixedKey)) {
            return false;
        }

        return (int) Cache::get($prefixedKey) >= $maxAttempts;
    }
}
