<?php

declare(strict_types=1);

namespace App\Core\Cache;

use App\Core\Application\App;

/**
 * Static proxy (facade) for the CacheManager.
 *
 * Provides convenient static access to the underlying cache store via
 * the application's dependency-injection container. Method calls are
 * forwarded to the resolved CacheManager instance.
 *
 * @method static mixed       get(string $key, mixed $default = null)
 * @method static bool        put(string $key, mixed $value, int $seconds)
 * @method static bool        has(string $key)
 * @method static bool        forget(string $key)
 * @method static bool        flush()
 * @method static mixed       remember(string $key, int $seconds, callable $callback)
 * @method static mixed       rememberForever(string $key, callable $callback)
 * @method static bool        forever(string $key, mixed $value)
 * @method static int|false   increment(string $key, int $value = 1)
 * @method static int|false   decrement(string $key, int $value = 1)
 * @method static CacheRepository store(?string $name = null)
 * @method static CacheRepository driver(?string $driver = null)
 *
 * @see CacheManager
 */
class Cache
{
    /**
     * Forward static method calls to the CacheManager instance.
     *
     * @param string       $method    The method name to call on the CacheManager.
     * @param array<mixed> $arguments The arguments to pass.
     *
     * @return mixed
     */
    public static function __callStatic(string $method, array $arguments): mixed
    {
        /** @var CacheManager $manager */
        $manager = App::container()->make(CacheManager::class);

        return $manager->$method(...$arguments);
    }
}
