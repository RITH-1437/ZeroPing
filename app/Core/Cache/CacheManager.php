<?php

namespace App\Core\Cache;

use App\Core\Cache\Drivers\ArrayCacheDriver;
use App\Core\Cache\Drivers\FileCacheDriver;
use App\Core\Cache\Drivers\NullCacheDriver;
use App\Core\Support\Config;

class CacheManager
{
    protected array $stores = [];

    public function __construct()
    {
        // Drivers are resolved via create*Driver() methods in resolve()
    }

    public function store(?string $name = null): CacheRepository
    {
        $name = $name ?: $this->getDefaultDriver();

        if (isset($this->stores[$name])) {
            return $this->stores[$name];
        }

        $config = Config::get("cache.stores.{$name}");

        return $this->stores[$name] = $this->resolve($name, $config);
    }

    public function driver(?string $driver = null): CacheRepository
    {
        return $this->store($driver);
    }

    protected function resolve(string $name, array $config): CacheRepository
    {
        $driverMethod = 'create' . ucfirst($name) . 'Driver';

        if (method_exists($this, $driverMethod)) {
            return $this->{$driverMethod}($config);
        } else {
            throw new \InvalidArgumentException("Driver [{$name}] is not supported.");
        }
    }

    protected function createFileDriver(array $config): CacheRepository
    {
        return new CacheRepository(new FileCacheDriver($config));
    }

    protected function createArrayDriver(): CacheRepository
    {
        return new CacheRepository(new ArrayCacheDriver());
    }

    protected function createNullDriver(): CacheRepository
    {
        return new CacheRepository(new NullCacheDriver());
    }

    public function getDefaultDriver(): string
    {
        return Config::get('cache.default');
    }

    public function __call(string $method, array $parameters)
    {
        return $this->store()->$method(...$parameters);
    }
}
