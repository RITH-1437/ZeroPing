<?php

namespace App\Core\Database\Drivers;

use App\Core\Database\Grammar\MySqlGrammar;
use PDO;
use PDOException;

/**
 * MySQL driver.
 *
 * MariaDB is wire-compatible with MySQL and therefore reuses this driver
 * (see MariaDbDriver). The only mysql-specific behaviour lives here so it
 * can be swapped without touching the ORM.
 */
class MySqlDriver implements DriverInterface
{
    public function getName(): string
    {
        return 'mysql';
    }

    public function getPdoExtension(): string
    {
        return 'pdo_mysql';
    }

    public function grammar(): \App\Core\Database\Grammar\Grammar
    {
        return new MySqlGrammar();
    }

    public function quoteIdentifier(string $name): string
    {
        return '`' . str_replace('`', '``', $name) . '`';
    }

    public function supports(string $feature): bool
    {
        return in_array($feature, ['savepoints', 'transactions', 'foreign_keys'], true);
    }

    public function connect(array $config): PDO
    {
        $host     = $config['host']     ?? '127.0.0.1';
        $port     = $config['port']     ?? 3306;
        $database = $config['database'] ?? '';
        $charset  = $config['charset']  ?? 'utf8mb4';
        $username = $config['username'] ?? $config['user'] ?? '';
        $password = $config['password'] ?? '';

        $dsn = sprintf(
            'mysql:host=%s;port=%s;charset=%s',
            $host,
            $port,
            $charset
        );

        if ($database !== '') {
            $dsn .= ';dbname=' . $database;
        }

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES  => false,
        ];

        try {
            return new PDO($dsn, $username, $password, $options);
        } catch (PDOException $e) {
            throw new \RuntimeException(
                "Could not connect to MySQL.\n" .
                "  Host: {$host}:{$port}\n" .
                "  Database: {$database}\n" .
                "  User: {$username}\n" .
                "  Error: " . $e->getMessage() . "\n" .
                "Check your DB_* environment variables and that MySQL is running."
            );
        }
    }
}
