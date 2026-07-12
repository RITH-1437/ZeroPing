<?php

namespace App\Core\Database;

use PDO;
use PDOException;

class MigrationRunner
{
    private PDO $db;

    private string $migrationPath;

    public function __construct()
    {
        $this->db = Database::connect();

        $this->migrationPath = BASE_PATH . '/database/migrations';
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

            if (in_array($migrationName, $executed)) {
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
     * Return the list of migration files that have not yet been executed.
     *
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

    /**
     * Run the "up" step of a single migration file and record it.
     */
    public function runUp(string $file, int $batch): void
    {
        $migrationName = basename($file);

        /** @var Migration|string $migration */
        $migration = require $file;

        try {
            if ($migration instanceof Migration) {
                $migration->up();
            } elseif (is_string($migration)) {
                // Raw SQL migration
                foreach (array_filter(array_map('trim', explode(';', $migration))) as $sql) {
                    $this->db->exec($sql);
                }
            }

            $stmt = $this->db->prepare("
                INSERT INTO migrations (migration, batch)
                VALUES (?, ?)
            ");

            $stmt->execute([
                $migrationName,
                $batch
            ]);
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    private function createMigrationTable(): void
    {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS migrations (

                id INT AUTO_INCREMENT PRIMARY KEY,

                migration VARCHAR(255) UNIQUE,

                batch INT,

                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP

            )
        ");
    }

    private function migrationFiles(): array
    {
        return glob($this->migrationPath . '/*.php');
    }

    private function executedMigrations(): array
    {
        $stmt = $this->db->query("
            SELECT migration
            FROM migrations
        ");

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function nextBatch(): int
    {
        $stmt = $this->db->query("
            SELECT MAX(batch)
            FROM migrations
        ");

        return (int)$stmt->fetchColumn() + 1;
    }

    private function lastBatch(): int
    {
        $stmt = $this->db->query("SELECT MAX(batch) FROM migrations");
        return (int) $stmt->fetchColumn();
    }

    private function getMigrationsInBatch(int $batch): array
    {
        $stmt = $this->db->prepare("SELECT migration FROM migrations WHERE batch = ? ORDER BY id ASC");
        $stmt->execute([$batch]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
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

        // Rollback all batches from highest to lowest
        $maxBatch = $this->lastBatch();

        if ($maxBatch === 0) {
            echo "Nothing to rollback, running migrations...\n\n";
        } else {
            echo "⬇️  Rolling back all migrations...\n\n";

            for ($batch = $maxBatch; $batch >= 1; $batch--) {
                $migrations = array_reverse($this->getMigrationsInBatch($batch));

                foreach ($migrations as $migrationName) {
                    $file = $this->migrationPath . '/' . $migrationName;

                    if (!file_exists($file)) {
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

                    $stmt = $this->db->prepare("DELETE FROM migrations WHERE migration = ?");
                    $stmt->execute([$migrationName]);
                }
            }

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

        $migrations = array_reverse($this->getMigrationsInBatch($lastBatch));

        foreach ($migrations as $migrationName) {
            $file = $this->migrationPath . '/' . $migrationName;

            if (!file_exists($file)) {
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

            $stmt = $this->db->prepare("DELETE FROM migrations WHERE migration = ?");
            $stmt->execute([$migrationName]);
        }

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

        for ($batch = $maxBatch; $batch >= 1; $batch--) {
            $migrations = array_reverse($this->getMigrationsInBatch($batch));

            foreach ($migrations as $migrationName) {
                $file = $this->migrationPath . '/' . $migrationName;

                if (!file_exists($file)) {
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

                $stmt = $this->db->prepare("DELETE FROM migrations WHERE migration = ?");
                $stmt->execute([$migrationName]);
            }
        }

        echo PHP_EOL . "🎉 Rollback completed successfully." . PHP_EOL;
    }

    public function fresh(): void
    {
        $this->createMigrationTable();

        // Drop all tables in the database (disable FK checks first)
        $this->db->exec("SET FOREIGN_KEY_CHECKS = 0");

        $tables = $this->db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);

        foreach ($tables as $table) {
            $this->db->exec("DROP TABLE IF EXISTS `{$table}`");
            echo "🗑️  Dropped table: {$table}\n";
        }

        $this->db->exec("SET FOREIGN_KEY_CHECKS = 1");

        echo PHP_EOL . "🔄 Re-running all migrations..." . PHP_EOL . PHP_EOL;

        $this->run();
    }
}
