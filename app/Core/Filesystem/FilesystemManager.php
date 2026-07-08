<?php

namespace App\Core\Filesystem;

use App\Core\Filesystem\Drivers\LocalDriver;
use App\Core\Filesystem\Drivers\NullDriver;
use App\Core\Support\Config;

class FilesystemManager
{
    protected array $disks = [];

    public function __construct()
    {
        // Drivers are resolved lazily via resolve() → create*Driver() methods
    }

    public function disk(string $name = null): FilesystemRepository
    {
        $name = $name ?: $this->getDefaultDriver();

        if (isset($this->disks[$name])) {
            return $this->disks[$name];
        }

        $config = Config::get("filesystem.disks.{$name}");

        return $this->disks[$name] = $this->resolve($name, $config);
    }

    public function driver(string $driver = null): FilesystemRepository
    {
        return $this->disk($driver);
    }

    protected function resolve(string $name, ?array $config): FilesystemRepository
    {
        if (!$config) {
            throw new \InvalidArgumentException("Disk [{$name}] is not configured.");
        }

        $driver = $config['driver'] ?? $name;
        $driverMethod = 'create' . ucfirst($driver) . 'Driver';

        if (method_exists($this, $driverMethod)) {
            return $this->{$driverMethod}($config);
        } else {
            throw new \InvalidArgumentException("Driver [{$driver}] is not supported.");
        }
    }

    protected function createLocalDriver(array $config): FilesystemRepository
    {
        return new FilesystemRepository(new LocalDriver($config));
    }

    protected function createNullDriver(): FilesystemRepository
    {
        return new FilesystemRepository(new NullDriver());
    }

    public function getDefaultDriver(): string
    {
        return Config::get('filesystem.default');
    }

    public function __call(string $method, array $parameters)
    {
        return $this->disk()->$method(...$parameters);
    }
}
