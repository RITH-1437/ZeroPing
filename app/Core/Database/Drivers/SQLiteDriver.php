<?php

namespace App\Core\Database\Drivers;

use App\Core\Database\Grammar\SQLiteGrammar;
use PDO;
use PDOException;

/**
 * SQLite driver — the default engine for new ZeroPing projects.
 *
 * Requires zero configuration: a single file (or :memory:) is enough to
 * build a fully working application.
 */
class SQLiteDriver implements DriverInterface
{
    public function getName(): string
    {
        return 'sqlite';
    }

    public function getPdoExtension(): string
    {
        return 'pdo_sqlite';
    }

    public function grammar(): \App\Core\Database\Grammar\Grammar
    {
        return new SQLiteGrammar();
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
        $database = $config['database'] ?? ':memory:';

        if ($database !== ':memory:') {
            $directory = dirname($database);

            if (!is_dir($directory) && !@mkdir($directory, 0775, true) && !is_dir($directory)) {
                throw new \RuntimeException("SQLite directory does not exist: {$directory}");
            }

            if (!is_writable($directory)) {
                throw new \RuntimeException("SQLite directory is not writable: {$directory}");
            }
        }

        $options = [
            PDO::ATTR_ERRMODE           => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES  => false,
        ];

        try {
            $pdo = new PDO('sqlite:' . $database, null, null, $options);
        } catch (PDOException $e) {
            throw new \RuntimeException(
                "Could not open the SQLite database.\n" .
                "  Path: {$database}\n" .
                "  Error: " . $e->getMessage()
            );
        }

        // SQLite honours foreign keys only when explicitly enabled per connection.
        $pdo->exec('PRAGMA foreign_keys = ON');

        return $pdo;
    }
}
