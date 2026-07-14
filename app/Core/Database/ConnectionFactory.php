<?php

namespace App\Core\Database;

use App\Core\Database\Drivers\DriverInterface;
use App\Core\Database\Drivers\MariaDbDriver;
use App\Core\Database\Drivers\MySqlDriver;
use App\Core\Database\Drivers\PostgreSqlDriver;
use App\Core\Database\Drivers\SQLiteDriver;

/**
 * Builds Connection objects from configuration.
 *
 * This is the only place that maps a connection name to a concrete driver
 * class. Adding SQL Server / Oracle later means extending this map (and the
 * DriverInterface implementations) — the ORM is unaffected.
 */
class ConnectionFactory
{
    /** @var array<string, class-string<DriverInterface>> */
    private array $drivers = [
        'sqlite'  => SQLiteDriver::class,
        'mysql'   => MySqlDriver::class,
        'mariadb' => MariaDbDriver::class,
        'pgsql'   => PostgreSqlDriver::class,
    ];

    /**
     * @param  class-string<DriverInterface>  $driverClass
     */
    public function registerDriver(string $name, string $driverClass): void
    {
        $this->drivers[$name] = $driverClass;
    }

    public function availableDrivers(): array
    {
        return array_keys($this->drivers);
    }

    public function hasDriver(string $name): bool
    {
        return isset($this->drivers[$name]);
    }

    /**
     * Resolve a driver instance for the given connection name.
     */
    public function driver(string $name): DriverInterface
    {
        if (!isset($this->drivers[$name])) {
            throw new \RuntimeException(
                "Unsupported database driver [{$name}]. Supported: "
                . implode(', ', array_keys($this->drivers)) . '.'
            );
        }

        $driverClass = $this->drivers[$name];

        return new $driverClass();
    }

    /**
     * Build a fully wired Connection for the given configuration.
     *
     * @param  array<string, mixed>  $config
     */
    public function make(string $name, array $config): Connection
    {
        $driver = $this->driver($config['driver'] ?? $name);

        return new Connection($driver, $driver->connect($config), $name);
    }
}
