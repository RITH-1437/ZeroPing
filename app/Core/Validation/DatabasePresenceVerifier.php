<?php

namespace App\Core\Validation;

use App\Core\Database\Database;
use PDO;

class DatabasePresenceVerifier
{
    protected PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    /**
     * Determine whether a value exists.
     */
    public function exists(
        string $table,
        string $column,
        mixed $value
    ): bool {
        return $this->countMatchingRows($table, $column, $value) > 0;
    }

    /**
     * Determine whether a value is unique.
     */
    public function unique(
        string $table,
        string $column,
        mixed $value
    ): bool {
        return $this->countMatchingRows($table, $column, $value) === 0;
    }

    /**
     * Count matching rows in the database.
     */
    protected function countMatchingRows(
        string $table,
        string $column,
        mixed $value
    ): int {
        $table = preg_replace('/[^a-zA-Z0-9_]/', '', $table);
        $column = preg_replace('/[^a-zA-Z0-9_]/', '', $column);

        $stmt = $this->db->prepare("
            SELECT COUNT(*)
            FROM {$table}
            WHERE {$column} = ?
        ");

        $stmt->execute([$value]);

        return (int) $stmt->fetchColumn();
    }
}