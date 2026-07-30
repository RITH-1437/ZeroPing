<?php

declare(strict_types=1);

namespace App\Core\Queue;

use App\Core\Queue\Drivers\ArrayDriver;
use App\Core\Queue\Drivers\DatabaseDriver;
use App\Core\Queue\Drivers\NullDriver;
use App\Core\Queue\Drivers\QueueDriver;
use App\Core\Queue\Drivers\SyncDriver;
use App\Core\Support\Config;

/**
 * Manages queue connections and resolves queue drivers.
 *
 * Acts as a factory and registry for queue connections. Connections
 * are resolved lazily and cached for the lifetime of the manager instance.
 * The manager supports 'sync', 'database', 'array', and 'null' drivers
 * out of the box and can be extended with custom drivers.
 *
 * @example
 * ```php
 * $manager = new QueueManager();
 * $manager->connection('database')->push($job);
 * $manager->connection()->pop('emails'); // uses default connection
 * ```
 */
class QueueManager
{
    /**
     * Resolved queue connection instances, keyed by connection name.
     *
     * @var array<string, QueueRepository>
     */
    protected array $connections = [];

    /**
     * Custom driver resolvers registered via `extend()`.
     *
     * @var array<string, callable>
     */
    protected array $customCreators = [];

    /**
     * Get a queue connection instance by name.
     *
     * If no name is given, the default connection is used. Connections
     * are resolved once and cached for subsequent calls.
     *
     * @param string|null $name The connection name, or null for default.
     * @return QueueRepository The queue repository for the given connection.
     *
     * @throws \InvalidArgumentException If the driver is not supported.
     */
    public function connection(?string $name = null): QueueRepository
    {
        $name = $name ?: $this->getDefaultDriver();

        if (isset($this->connections[$name])) {
            return $this->connections[$name];
        }

        $config = Config::get("queue.connections.{$name}") ?? [];

        return $this->connections[$name] = $this->resolve($name, $config);
    }

    /**
     * Alias for `connection()`.
     *
     * @param string|null $driver The driver/connection name.
     * @return QueueRepository
     */
    public function driver(?string $driver = null): QueueRepository
    {
        return $this->connection($driver);
    }

    /**
     * Register a custom queue driver resolver.
     *
     * @param string $driver The driver name.
     * @param callable $callback A factory callable that receives the config array and returns a QueueDriver.
     * @return static
     */
    public function extend(string $driver, callable $callback): static
    {
        $this->customCreators[$driver] = $callback;

        return $this;
    }

    /**
     * Resolve a queue connection by driver name.
     *
     * Checks for custom creators first, then falls back to built-in driver factories.
     *
     * @param string $name The driver/connection name.
     * @param array<string, mixed> $config The connection configuration.
     * @return QueueRepository The resolved queue repository.
     *
     * @throws \InvalidArgumentException If the driver cannot be resolved.
     */
    protected function resolve(string $name, array $config): QueueRepository
    {
        // Check custom creators first
        if (isset($this->customCreators[$name])) {
            $driver = ($this->customCreators[$name])($config);
            return new QueueRepository($driver);
        }

        // Determine the driver type from config or use the connection name
        $driverType = $config['driver'] ?? $name;

        return match ($driverType) {
            'sync' => $this->createSyncDriver(),
            'database' => $this->createDatabaseDriver($config),
            'array' => $this->createArrayDriver(),
            'null' => $this->createNullDriver(),
            default => throw new \InvalidArgumentException(
                sprintf('Queue driver [%s] is not supported.', $driverType)
            ),
        };
    }

    /**
     * Create a synchronous queue driver instance.
     *
     * The sync driver executes jobs immediately within the current process.
     *
     * @return QueueRepository
     */
    protected function createSyncDriver(): QueueRepository
    {
        return new QueueRepository(new SyncDriver());
    }

    /**
     * Create a database queue driver instance.
     *
     * @param array<string, mixed> $config The database connection configuration.
     * @return QueueRepository
     */
    protected function createDatabaseDriver(array $config): QueueRepository
    {
        return new QueueRepository(new DatabaseDriver($config));
    }

    /**
     * Create an in-memory array queue driver instance.
     *
     * Useful for testing — jobs are stored in memory and lost on process exit.
     *
     * @return QueueRepository
     */
    protected function createArrayDriver(): QueueRepository
    {
        return new QueueRepository(new ArrayDriver());
    }

    /**
     * Create a null/no-op queue driver instance.
     *
     * All jobs dispatched to this driver are silently discarded.
     *
     * @return QueueRepository
     */
    protected function createNullDriver(): QueueRepository
    {
        return new QueueRepository(new NullDriver());
    }

    /**
     * Get the name of the default queue driver/connection.
     *
     * @return string The default driver name (falls back to 'sync').
     */
    public function getDefaultDriver(): string
    {
        return Config::get('queue.default') ?? 'sync';
    }

    /**
     * Get all resolved connections.
     *
     * @return array<string, QueueRepository>
     */
    public function getConnections(): array
    {
        return $this->connections;
    }

    /**
     * Dynamically pass method calls to the default connection.
     *
     * @param string $method The method name.
     * @param array<int, mixed> $parameters The method parameters.
     * @return mixed
     */
    public function __call(string $method, array $parameters): mixed
    {
        return $this->connection()->{$method}(...$parameters);
    }
}
