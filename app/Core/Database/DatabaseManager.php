<?php

namespace App\Core\Database;

/**
 * The single entry point the ORM, query builder, migrations and the rest of
 * the framework use to reach a database.
 *
 * Connections are created lazily (through the ConnectionFactory) and cached.
 * To support a new engine (SQL Server, Oracle, …) you register one more
 * driver in the factory — nothing else in the framework has to change.
 */
class DatabaseManager
{
    private ConnectionFactory $factory;

    /** @var array<string, Connection> */
    private array $connections = [];

    private ?string $default = null;

    public function __construct(?ConnectionFactory $factory = null)
    {
        $this->factory = $factory ?? new ConnectionFactory();
    }

    /**
     * @param  class-string<\App\Core\Database\Drivers\DriverInterface>  $driverClass
     */
    public function registerDriver(string $name, string $driverClass): void
    {
        $this->factory->registerDriver($name, $driverClass);
    }

    public function availableDrivers(): array
    {
        return $this->factory->availableDrivers();
    }

    public function getDefaultConnection(): string
    {
        if ($this->default === null) {
            $this->default = config('database.default', 'sqlite');
        }

        return $this->default;
    }

    /**
     * @param  string|null  $name  Connection name; null resolves the default.
     */
    public function connection(?string $name = null): Connection
    {
        $name = $name ?? $this->getDefaultConnection();

        if (!isset($this->connections[$name])) {
            $this->connections[$name] = $this->make($name);
        }

        return $this->connections[$name];
    }

    /**
     * Build (and cache) a raw PDO for the given connection name.
     */
    public function pdo(?string $name = null): \PDO
    {
        return $this->connection($name)->pdo();
    }

    /**
     * Replace the resolved connection (used by tests to inject in-memory DBs).
     */
    public function setConnection(string $name, Connection $connection): void
    {
        $this->connections[$name] = $connection;
    }

    public function purge(?string $name = null): void
    {
        if ($name === null) {
            $this->connections = [];

            return;
        }

        unset($this->connections[$name]);
    }

    /**
     * Build a Connection object from configuration.
     */
    public function make(string $name): Connection
    {
        return $this->factory->make($name, $this->configurationFor($name));
    }

    /**
     * Read a connection's configuration from config/database.php.
     *
     * @return array<string, mixed>
     */
    public function configurationFor(string $name): array
    {
        $connections = config('database.connections', []);

        if (!isset($connections[$name])) {
            throw new \RuntimeException(
                "Database connection [{$name}] is not defined in config/database.php."
            );
        }

        return (array) $connections[$name];
    }

    /**
     * The PDO extension required by the active (or given) connection.
     */
    public function requiredExtension(?string $name = null): string
    {
        $name = $name ?? $this->getDefaultConnection();

        return $this->connection($name)->driver()->getPdoExtension();
    }
}
