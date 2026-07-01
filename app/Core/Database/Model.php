<?php

namespace App\Core\Database;
use PDO;

abstract class Model
{
    protected PDO $db;

    protected string $table;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function all(): array
    {
        $stmt = $this->db->query(
            "SELECT * FROM {$this->table}"
        );

        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table} WHERE id = ?"
        );

        $stmt->execute([$id]);

        return $stmt->fetch() ?: null;
    }

    public function findBy(string $column, $value): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table} WHERE {$column} = ? LIMIT 1"
        );

        $stmt->execute([$value]);

        return $stmt->fetch() ?: null;
    }

    public function create(array $data): bool
    {
        $columns = implode(',', array_keys($data));

        $placeholders = implode(',', array_fill(0, count($data), '?'));

        $stmt = $this->db->prepare(
            "INSERT INTO {$this->table}
        ($columns)
        VALUES
        ($placeholders)"
        );

        return $stmt->execute(array_values($data));
    }

    public function update(int $id, array $data): bool
    {
        $fields = [];

        foreach ($data as $column => $value) {

            $fields[] = "{$column}=?";
        }

        $sql = implode(',', $fields);

        $stmt = $this->db->prepare(
            "UPDATE {$this->table}
        SET {$sql}
        WHERE id=?"
        );

        $values = array_values($data);

        $values[] = $id;

        return $stmt->execute($values);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare(
            "DELETE FROM {$this->table}
        WHERE id=?"
        );

        return $stmt->execute([$id]);
    }
}