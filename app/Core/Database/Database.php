<?php

namespace App\Core\Database;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $connection = null;
    private static array $log = [];

    public static function connect(): PDO
    {
        if (self::$connection === null) {
            $configPath = dirname(__FILE__, 3) . '/config/database.php';
            $dbConfig = file_exists($configPath) ? require $configPath : [];
            $host = $dbConfig['host'] ?? (defined('DB_HOST') ? DB_HOST : '127.0.0.1');
            $dbname = $dbConfig['database'] ?? (defined('DB_NAME') ? DB_NAME : '');
            $user = $dbConfig['username'] ?? (defined('DB_USER') ? DB_USER : '');
            $pass = $dbConfig['password'] ?? (defined('DB_PASS') ? DB_PASS : '');
            $charset = $dbConfig['charset'] ?? (defined('DB_CHARSET') ? DB_CHARSET : 'utf8mb4');
            $port = $dbConfig['port'] ?? (defined('DB_PORT') ? DB_PORT : 3306);

            $dsn = sprintf(
                "mysql:host=%s;dbname=%s;port=%s;charset=%s",
                $host,
                $dbname,
                $port,
                $charset
            );

            try {
                self::$connection = new PDO(
                    $dsn,
                    $user,
                    $pass,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                    ]
                );
            } catch (PDOException $e) {
                throw new \RuntimeException(
                    "Database connection failed.\n" .
                    "  Host: {$host}\n" .
                    "  User: {$user}\n" .
                    "  Error: {$e->getMessage()}\n" .
                    "Check your .env database credentials and ensure MySQL is running."
                );
            }

            // The profiled statement class adds overhead on every query; only
            // enable it when debugging so production queries stay lean.
            if (defined('APP_DEBUG') && APP_DEBUG === true) {
                self::$connection->setAttribute(
                    PDO::ATTR_STATEMENT_CLASS,
                    [ProfiledStatement::class, [self::$connection]]
                );
            }
        }

        return self::$connection;
    }

    public static function getSchemaBuilder(): Schema
    {
        return new Schema(self::connect());
    }

    public static function addLog(string $query, array $bindings, float $time): void
    {
        self::$log[] = [
            'query' => $query,
            'bindings' => $bindings,
            'time' => $time,
        ];
    }

    public static function getLog(): array
    {
        return self::$log;
    }
}
