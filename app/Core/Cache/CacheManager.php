<?php

declare(strict_types=1);

namespace App\Core\Cache;

use App\Core\Cache\Drivers\ArrayCacheDriver;
use App\Core\Cache\Drivers\FileCacheDriver;
use App\Core\Cache\Drivers\NullCacheDriver;
use App\Core\Support\Config;
use InvalidArgumentException;

/**
 * Cache manager responsible for resolving and managing cache store instances.
 *
 * Acts as a factory and registry for cache repositories. Each named store
 * is resolved on first access and reused for subsequent calls.
 *
 * @method mixed       get(string $key, mixed $default = null)
 * @method bool        put(string $key, mixed $value, int $seconds)
 * @method bool        has(string $key)
 * @method bool        forget(string $key)
 * @method bool        flush()
 * @method mixed       remember(string $key, int $seconds, callable $callback)
 * @method mixed       rememberForever(string $key, callable $callback)
 * @method bool        forever(string $key, mixed $value)
 * @method int|false   increment(string $key, int $value = 1)
 * @method int|false   decrement(string $key, int $value = 1)
 */
class CacheManager
{
    /**
     * Resolved store instances keyed by driver name.
     *
     * @var array<string, CacheRepository>
     */
    protected array $stores = [];

    /**
     * Resolve and return a named cache store.
     *
     * @param string|null $name The store name (null for the default driver).
     *
     * @return CacheRepository
     *
     * @throws InvalidArgumentException If the driver is not supported.
     */
    public function store(?string $name = null): CacheRepository
    {
        $name = $name ?: $this->getDefaultDriver();

        if (isset($this->stores[$name])) {
            return $this->stores[$name];
        }

        $config = Config::get("cache.stores.{$name}", []);

        return $this->stores[$name] = $this->resolve($name, $config);
    }

    /**
     * Alias for the store() method.
     *
     * @param string|null $driver The driver name (null for the default).
     *
     * @return CacheRepository
     */
    public function driver(?string $driver = null): CacheRepository
    {
        return $this->store($driver);
    }

    /**
     * Resolve a cache repository for the given driver name.
     *
     * @param string              $name   The driver name.
     * @param array<string,mixed> $config The driver configuration.
     *
     * @return CacheRepository
     *
     * @throws InvalidArgumentException If the driver is not supported.
     */
    protected function resolve(string $name, array $config): CacheRepository
    {
        $driverMethod = 'create' . ucfirst($name) . 'Driver';

        if (method_exists($this, $driverMethod)) {
            return $this->{$driverMethod}($config);
        }

        throw new InvalidArgumentException("Cache driver [{$name}] is not supported.");
    }

    /**
     * Create a file cache driver instance.
     *
     * @param array{path: string} $config The file driver configuration.
     *
     * @return CacheRepository
     */
    protected function createFileDriver(array $config): CacheRepository
    {
        return new CacheRepository(new FileCacheDriver($config));
    }

    /**
     * Create an array cache driver instance.
     *
     * @param array<string,mixed> $config The array driver configuration (unused).
     *
     * @return CacheRepository
     */
    protected function createArrayDriver(array $config = []): CacheRepository
    {
        return new CacheRepository(new ArrayCacheDriver());
    }

    /**
     * Create a null cache driver instance.
     *
     * @param array<string,mixed> $config The null driver configuration (unused).
     *
     * @return CacheRepository
     */
    protected function createNullDriver(array $config = []): CacheRepository
    {
        return new CacheRepository(new NullCacheDriver());
    }

    /**
     * Return the default cache driver name from configuration.
     *
     * @return string
     */
    public function getDefaultDriver(): string
    {
        return Config::get('cache.default', 'file');
    }

    /**
     * Dynamically call methods on the default cache store.
     *
     * @param string       $method     The method name to call.
     * @param array<mixed> $parameters The arguments to pass.
     *
     * @return mixed
     */
    public function __call(string $method, array $parameters): mixed
    {
        return $this->store()->$method(...$parameters);
    }
}
