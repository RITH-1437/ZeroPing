<?php

namespace App\Core\Database\Drivers;

use App\Core\Database\Grammar\PostgreSqlGrammar;
use PDO;
use PDOException;

/**
 * PostgreSQL driver.
 */
class PostgreSqlDriver implements DriverInterface
{
    public function getName(): string
    {
        return 'pgsql';
    }

    public function getPdoExtension(): string
    {
        return 'pdo_pgsql';
    }

    public function grammar(): \App\Core\Database\Grammar\Grammar
    {
        return new PostgreSqlGrammar();
    }

    public function quoteIdentifier(string $name): string
    {
        return '"' . str_replace('"', '""', $name) . '"';
    }

    public function supports(string $feature): bool
    {
        return in_array($feature, ['savepoints', 'transactions', 'foreign_keys'], true);
    }

    public function connect(array $config): PDO
    {
        $host     = $config['host']     ?? '127.0.0.1';
        $port     = $config['port']     ?? 5432;
        $database = $config['database'] ?? '';
        $username = $config['username'] ?? $config['user'] ?? '';
        $password = $config['password'] ?? '';
        $charset  = $config['charset']  ?? 'utf8';
        $schema   = $config['schema']   ?? 'public';

        $dsn = sprintf(
            'pgsql:host=%s;port=%s;dbname=%s',
            $host,
            $port,
            $database
        );

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES  => false,
        ];

        try {
            $pdo = new PDO($dsn, $username, $password, $options);

            if ($charset !== '') {
                $pdo->exec('SET client_encoding TO ' . $pdo->quote($charset));
            }

            // Keep PostgreSQL predictable for our grammar by defaulting to the
            // standard "public" schema first.
            $pdo->exec('SET search_path TO ' . $pdo->quote($schema));

            return $pdo;
        } catch (PDOException $e) {
            throw new \RuntimeException(
                "Could not connect to PostgreSQL.\n" .
                "  Host: {$host}:{$port}\n" .
                "  Database: {$database}\n" .
                "  User: {$username}\n" .
                "  Error: " . $e->getMessage() . "\n" .
                "Check your DB_* environment variables and that PostgreSQL is running."
            );
        }
    }
}
