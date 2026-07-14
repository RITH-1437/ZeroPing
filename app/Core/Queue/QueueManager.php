<?php

namespace App\Core\Queue;

use App\Core\Queue\Drivers\ArrayDriver;
use App\Core\Queue\Drivers\DatabaseDriver;
use App\Core\Queue\Drivers\NullDriver;
use App\Core\Queue\Drivers\SyncDriver;
use App\Core\Support\Config;

class QueueManager
{
    protected array $connections = [];

    public function __construct()
    {
        // Drivers are resolved via create*Driver() methods in resolve()
    }

    public function connection(string $name = null): QueueRepository
    {
        $name = $name ?: $this->getDefaultDriver();

        if (isset($this->connections[$name])) {
            return $this->connections[$name];
        }

        $config = Config::get("queue.connections.{$name}") ?? [];

        return $this->connections[$name] = $this->resolve($name, $config);
    }

    public function driver(string $driver = null): QueueRepository
    {
        return $this->connection($driver);
    }

    protected function resolve(string $name, array $config): QueueRepository
    {
        $driverMethod = 'create' . ucfirst($name) . 'Driver';

        if (method_exists($this, $driverMethod)) {
            return $this->{$driverMethod}($config);
        } else {
            throw new \InvalidArgumentException("Driver [{$name}] is not supported.");
        }
    }

    protected function createSyncDriver(): QueueRepository
    {
        return new QueueRepository(new SyncDriver());
    }

    protected function createDatabaseDriver(array $config): QueueRepository
    {
        return new QueueRepository(new DatabaseDriver($config));
    }

    protected function createArrayDriver(): QueueRepository
    {
        return new QueueRepository(new ArrayDriver());
    }

    protected function createNullDriver(): QueueRepository
    {
        return new QueueRepository(new NullDriver());
    }

    public function getDefaultDriver(): string
    {
        return Config::get('queue.default') ?? 'sync';
    }

    public function __call(string $method, array $parameters)
    {
        return $this->connection()->$method(...$parameters);
    }
}
