<?php

declare(strict_types=1);

namespace App\Core\Testing\Database;

use App\Core\Database\Database;
use App\Core\Database\QueryBuilder;

/**
 * Database assertions usable from test cases (e.g. assertDatabaseHas).
 */
trait DatabaseAssertions
{
    public function assertDatabaseHas(string $table, array $data): void
    {
        $this->assertTrue(
            $this->query($table, $data)->count() > 0,
            "Failed asserting that table '{$table}' contains " . $this->format($data) . '.'
        );
    }

    public function assertDatabaseMissing(string $table, array $data): void
    {
        $this->assertFalse(
            $this->query($table, $data)->count() > 0,
            "Failed asserting that table '{$table}' does not contain " . $this->format($data) . '.'
        );
    }

    protected function query(string $table, array $data): QueryBuilder
    {
        $query = $this->table($table);

        foreach ($data as $column => $value) {
            $query->where($column, $value);
        }

        return $query;
    }

    protected function table(string $table): QueryBuilder
    {
        return new QueryBuilder(Database::connect(), $table);
    }

    protected function format(array $data): string
    {
        $parts = [];

        foreach ($data as $key => $value) {
            $parts[] = "{$key}={$value}";
        }

        return '[' . implode(', ', $parts) . ']';
    }
}
