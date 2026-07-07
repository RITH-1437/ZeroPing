<?php

namespace App\Core\Testing\Database;

use App\Core\Database\Database;

trait DatabaseAssertions
{
    public function assertDatabaseHas(string $table, array $data): void
    {
        $this->assertTrue(
            $this->getDatabase()->table($table)->where($data)->exists(),
            "Failed asserting that table [{$table}] has matching row."
        );
    }

    public function assertDatabaseMissing(string $table, array $data): void
    {
        $this->assertFalse(
            $this->getDatabase()->table($table)->where($data)->exists(),
            "Failed asserting that table [{$table}] does not have matching row."
        );
    }

    public function assertTableExists(string $table): void
    {
        $this->assertTrue(
            $this->getDatabase()->getSchemaBuilder()->hasTable($table),
            "Failed asserting that table [{$table}] exists."
        );
    }

    protected function getDatabase()
    {
        return Database::connect();
    }
}
