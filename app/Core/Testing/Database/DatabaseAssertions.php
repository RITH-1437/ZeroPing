<?php

namespace App\Core\Testing\Database;

use App\Core\Database\Database;

trait DatabaseAssertions
{
    public function assertDatabaseHas(string $table, array $data): void
    {
        $this->assertTrue(
            $this->rowExists($table, $data),
            "Failed asserting that table [{$table}] has matching row."
        );
    }

    public function assertDatabaseMissing(string $table, array $data): void
    {
        $this->assertFalse(
            $this->rowExists($table, $data),
            "Failed asserting that table [{$table}] does not have matching row."
        );
    }

    public function assertTableExists(string $table): void
    {
        $pdo = Database::connect();
        $stmt = $pdo->query("SHOW TABLES LIKE " . $pdo->quote($table));
        $this->assertTrue(
            $stmt->fetchColumn() !== false,
            "Failed asserting that table [{$table}] exists."
        );
    }

    protected function rowExists(string $table, array $data): bool
    {
        $pdo = Database::connect();
        $wheres = implode(' AND ', array_map(fn($k) => "{$k} = ?", array_keys($data)));
        $stmt = $pdo->prepare("SELECT 1 FROM `{$table}` WHERE {$wheres} LIMIT 1");
        $stmt->execute(array_values($data));
        return $stmt->fetchColumn() !== false;
    }
}
