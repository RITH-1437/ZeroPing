<?php

namespace App\Core\Database;

use App\Core\Database\Grammar\Grammar;
use PDO;

/**
 * Runs the database migrations.
 *
 * All SQL this runner produces is compiled through the active Grammar, so the
 * same migrations run on SQLite, MySQL, MariaDB and PostgreSQL without
 * modification.
 */
class MigrationRunner
{
    /**
     * Package migration directories pushed in by service providers via
     * loadMigrationsFrom() (see Zeroping\Support\Foundation\MigrationLoader).
     *
     * @var string[]
     */
    private static array $extraPaths = [];

    private Connection $connection;

    /**
     * @var string[]
     */
    private array $migrationPaths;

    public function __construct(?Connection $connection = null)
    {
        $this->connection = $connection ?? Database::connection();

        $paths = [BASE_PATH . '/database/migrations'];

        foreach (self::$extraPaths as $path) {
            if (!in_array($path, $paths, true)) {
                $paths[] = $path;
            }
        }

        $this->migrationPaths = $paths;
    }

    /**
     * Register an additional migration directory (called by package providers).
     */
    public static function addPath(string $path): void
    {
        $path = rtrim($path, '/');

        if (!in_array($path, self::$extraPaths, true)) {
            self::$extraPaths[] = $path;
        }
    }

    /**
     * Clear the registered package paths (used by tests / optimize:clear).
     */
    public static function clearPaths(): void
    {
        self::$extraPaths = [];
    }

    public function run(): void
    {
        $this->createMigrationTable();

        $files = $this->migrationFiles();

        if (empty($files)) {
            echo "No migration files found.\n";

            return;
        }

        $executed = $this->executedMigrations();
        $batch = $this->nextBatch();

        foreach ($files as $file) {
            $migrationName = basename($file);

            if (in_array($migrationName, $executed, true)) {
                echo "⏩ {$migrationName}\n";
                continue;
            }

            echo "🚀 {$migrationName} ... ";
            $this->runUp($file, $batch);
            echo "Done ✅\n";
        }

        echo PHP_EOL . "🎉 Migration completed successfully." . PHP_EOL;
    }

    /**
     * @return string[]
     */
    public function pendingMigrationFiles(): array
    {
        $this->createMigrationTable();

        $executed = $this->executedMigrations();
        $pending = [];

        foreach ($this->migrationFiles() as $file) {
            if (!in_array(basename($file), $executed, true)) {
                $pending[] = $file;
            }
        }

        return $pending;
    }

    public function runUp(string $file, int $batch): void
    {
        $migrationName = basename($file);
        $migration = require $file;

        try {
            if ($migration instanceof Migration) {
                $migration->up();
            } elseif (is_string($migration)) {
                foreach (array_filter(array_map('trim', explode(';', $migration))) as $sql) {
                    $this->connection->statement($sql);
                }
            }

            $this->connection->statement(
                $this->connection->grammar()->compileInsertMigration(),
                [$migrationName, $batch]
            );
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    private function createMigrationTable(): void
    {
        $sql = $this->connection->grammar()->compileMigrationTable();
        $this->connection->statement($sql);
    }

    /**
     * @return string[]
     */
    private function migrationFiles(): array
    {
        $files = [];

        foreach ($this->migrationPaths as $path) {
            foreach (glob($path . '/*.php') ?: [] as $file) {
                $files[] = $file;
            }
        }

        return $files;
    }

    /**
     * Locate a migration file by name across all registered paths.
     */
    private function findMigrationFile(string $name): ?string
    {
        foreach ($this->migrationPaths as $path) {
            $file = $path . '/' . $name;

            if (file_exists($file)) {
                return $file;
            }
        }

        return null;
    }

    private function executedMigrations(): array
    {
        return array_map(
            static fn ($row) => array_values($row)[0],
            $this->connection->select($this->connection->grammar()->selectExecutedMigrations())
        );
    }

    public function nextBatch(): int
    {
        $rows = $this->connection->select($this->connection->grammar()->selectMaxBatch());
        $first = $rows[0] ?? [0];

        return ((int) (reset($first))) + 1;
    }

    private function lastBatch(): int
    {
        $rows = $this->connection->select($this->connection->grammar()->selectMaxBatch());
        $first = $rows[0] ?? [0];

        return (int) (reset($first));
    }

    private function getMigrationsInBatch(int $batch): array
    {
        $rows = $this->connection->select(
            $this->connection->grammar()->selectMigrationsInBatch(),
            [$batch]
        );

        return array_map(static fn ($row) => array_values($row)[0], $rows);
    }

    public function getMigrations(): array
    {
        return array_map('basename', $this->migrationFiles());
    }

    public function getRanMigrations(): array
    {
        return $this->executedMigrations();
    }

    public function refresh(): void
    {
        $this->createMigrationTable();

        $maxBatch = $this->lastBatch();

        if ($maxBatch === 0) {
            echo "Nothing to rollback, running migrations...\n\n";
        } else {
            echo "⬇️  Rolling back all migrations...\n\n";
            $this->rollbackBatches($maxBatch);
            echo PHP_EOL . "🔄 Re-running all migrations..." . PHP_EOL . PHP_EOL;
        }

        $this->run();
    }

    public function rollback(): void
    {
        $this->createMigrationTable();

        $lastBatch = $this->lastBatch();

        if (!$lastBatch) {
            echo "Nothing to rollback.\n";

            return;
        }

        $this->rollbackBatches($lastBatch);
        echo PHP_EOL . "🎉 Rollback completed successfully." . PHP_EOL;
    }

    public function reset(): void
    {
        $this->createMigrationTable();

        $maxBatch = $this->lastBatch();

        if ($maxBatch === 0) {
            echo "Nothing to rollback.\n";

            return;
        }

        echo "⬇️  Rolling back all migrations...\n\n";
        $this->rollbackBatches($maxBatch);
        echo PHP_EOL . "🎉 Rollback completed successfully." . PHP_EOL;
    }

    /**
     * Roll back batches from $maxBatch down to 1, running each migration's
     * down() method and removing its tracking row.
     */
    private function rollbackBatches(int $maxBatch): void
    {
        for ($batch = $maxBatch; $batch >= 1; $batch--) {
            $migrations = array_reverse($this->getMigrationsInBatch($batch));

            foreach ($migrations as $migrationName) {
                $file = $this->findMigrationFile($migrationName);

                if ($file === null || !file_exists($file)) {
                    echo "⚠️  File not found: {$migrationName}\n";
                    continue;
                }

                $migration = require $file;

                if (!$migration instanceof Migration) {
                    echo "⚠️  Skipping {$migrationName} (no down() method)\n";
                } else {
                    echo "⬇️  {$migrationName} ... ";
                    try {
                        $migration->down();
                        echo "Done ✅\n";
                    } catch (\Throwable $e) {
                        echo "Failed ❌\n";
                        throw $e;
                    }
                }

                $this->connection->statement(
                    $this->connection->grammar()->compileDeleteMigration(),
                    [$migrationName]
                );
            }
        }
    }

    public function fresh(): void
    {
        $this->createMigrationTable();

        $tables = $this->connection->listTables();

        if ($tables !== []) {
            $this->disableForeignKeys();
            foreach ($tables as $table) {
                $this->connection->statement($this->connection->grammar()->compileDrop($table));
                echo "🗑️  Dropped table: {$table}\n";
            }
            $this->enableForeignKeys();
        }

        echo PHP_EOL . "🔄 Re-running all migrations..." . PHP_EOL . PHP_EOL;
        $this->run();
    }

    // ── Grammar helpers ──────────────────────────────────────────────────

    private function grammar(): Grammar
    {
        return $this->connection->grammar();
    }

    private function disableForeignKeys(): void
    {
        $sql = $this->connection->grammar()->disableForeignKeys();
        if ($sql !== '') {
            $this->connection->statement($sql);
        }
    }

    private function enableForeignKeys(): void
    {
        $sql = $this->connection->grammar()->enableForeignKeys();
        if ($sql !== '') {
            $this->connection->statement($sql);
        }
    }
}
