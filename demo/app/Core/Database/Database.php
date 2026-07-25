<?php

namespace App\Core\Database;

use App\Core\Database\Drivers\DriverInterface;
use PDO;
use PDOException;

/**
 * Backwards-compatible facade over the DatabaseManager.
 *
 * Older code (and third-party providers) still call Database::connect(),
 * Database::getSchemaBuilder(), Database::addLog() and Database::getLog().
 * Those now resolve through the multi-driver DatabaseManager so the whole
 * framework benefits from SQLite / MySQL / MariaDB / PostgreSQL support
 * without call-site changes.
 */
class Database
{
    private static ?DatabaseManager $manager = null;

    /** @var array<string, array{query:string, bindings:array, time:float}> */
    private static array $log = [];

    public static function manager(): DatabaseManager
    {
        if (self::$manager === null) {
            self::$manager = new DatabaseManager();
        }

        return self::$manager;
    }

    public static function setManager(DatabaseManager $manager): void
    {
        self::$manager = $manager;
    }

    /**
     * Return the PDO for the default (or named) connection.
     */
    public static function connect(?string $name = null): PDO
    {
        $connection = self::manager()->connection($name);

        $pdo = $connection->pdo();

        // Profiling adds per-statement overhead; only enable it while debugging.
        if (defined('APP_DEBUG') && APP_DEBUG === true && !defined('ZEROPING_DB_PROFILE')) {
            $pdo->setAttribute(PDO::ATTR_STATEMENT_CLASS, [ProfiledStatement::class, [$pdo]]);
            define('ZEROPING_DB_PROFILE', true);
        }

        return $pdo;
    }

    public static function connection(?string $name = null): Connection
    {
        return self::manager()->connection($name);
    }

    /**
     * Inject a connection (used by tests to swap in an in-memory DB).
     */
    public static function setConnection(string $name, Connection $connection): void
    {
        self::manager()->setConnection($name, $connection);
    }

    public static function getConnection(?string $name = null): Connection
    {
        return self::manager()->connection($name);
    }

    public static function getSchemaBuilder(): Schema
    {
        return new Schema(self::connection());
    }

    public static function getDriverName(?string $name = null): string
    {
        return self::connection($name)->driver()->getName();
    }

    /**
     * @internal used by ProfiledStatement
     */
    public static function addLog(string $query, array $bindings, float $time): void
    {
        self::$log[] = [
            'query'    => $query,
            'bindings' => $bindings,
            'time'     => $time,
        ];
    }

    public static function getLog(): array
    {
        return self::$log;
    }

    /**
     * Resolve the driver instance for the (default) connection.
     */
    public static function driver(?string $name = null): DriverInterface
    {
        return self::connection($name)->driver();
    }
}
