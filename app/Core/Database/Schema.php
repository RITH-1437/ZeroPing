<?php

namespace App\Core\Database;

use PDO;

class Schema
{
    protected PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public static function create(string $table, \Closure $callback): void
    {
        $blueprint = new Blueprint($table);
        $callback($blueprint);
        $queries = $blueprint->toSql();
        foreach ($queries as $query) {
            Database::connect()->exec($query);
        }
    }

    public static function drop(string $table): void
    {
        $query = "DROP TABLE IF EXISTS {$table}";
        Database::connect()->exec($query);
    }

    public function hasTable(string $table): bool
    {
        $query = "SHOW TABLES LIKE '{$table}'";
        $stmt = $this->db->query($query);
        return $stmt->rowCount() > 0;
    }
}
