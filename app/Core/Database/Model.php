<?php

namespace App\Core\Database;
use App\Core\Database\QueryBuilder;

use PDO;

abstract class Model
{
    protected PDO $db;

    protected string $table;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    /**
     * Create a new query builder instance.
     */
    public function query(): QueryBuilder
    {
        return new QueryBuilder(
            $this->db,
            $this->table
        );
    }

    /**
     * Get all records.
     */
    public function all(): array
    {
        return $this->query()->get();
    }

    /**
     * Find a record by primary key.
     */
    public function find(int $id): ?array
    {
        return $this->query()
            ->where('id', $id)
            ->first();
    }

    /**
     * Find first record by column.
     */
    public function findBy(string $column, mixed $value): ?array
    {
        return $this->query()
            ->where($column, $value)
            ->first();
    }

    /**
     * Insert a new record.
     */
    public function create(array $data): bool
    {
        $columns = implode(',', array_keys($data));

        $placeholders = implode(',', array_fill(0, count($data), '?'));

        $stmt = $this->db->prepare(
            "INSERT INTO {$this->table} ({$columns})
             VALUES ({$placeholders})"
        );

        return $stmt->execute(array_values($data));
    }

    /**
     * Update a record.
     */
    public function update(int $id, array $data): bool
    {
        $fields = [];

        foreach ($data as $column => $value) {
            $fields[] = "{$column} = ?";
        }

        $sql = implode(', ', $fields);

        $stmt = $this->db->prepare(
            "UPDATE {$this->table}
             SET {$sql}
             WHERE id = ?"
        );

        $values = array_values($data);
        $values[] = $id;

        return $stmt->execute($values);
    }

    /**
     * Delete a record.
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare(
            "DELETE FROM {$this->table}
             WHERE id = ?"
        );

        return $stmt->execute([$id]);
    }
}