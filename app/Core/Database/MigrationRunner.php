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

            /** @var Migration $migration */
            $migration = require $file;

            try {

                $this->db->beginTransaction();

                $migration->up();

                $stmt = $this->db->prepare("
                INSERT INTO migrations (migration, batch)
                VALUES (?, ?)
            ");

                $stmt->execute([
                    $migrationName,
                    $batch
                ]);

                $this->db->commit();

                echo "Done ✅\n";

            } catch (\Throwable $e) {

                $this->db->rollBack();

                echo "Failed ❌\n";

                throw $e;
            }
        }

        echo PHP_EOL . "🎉 Migration completed successfully." . PHP_EOL;
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

    private function nextBatch(): int
    {
        $stmt = $this->db->query("
            SELECT MAX(batch)
            FROM migrations
        ");

        return (int)$stmt->fetchColumn() + 1;
    }
}