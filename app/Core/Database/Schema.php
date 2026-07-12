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

    public static function table(string $table, \Closure $callback): void
    {
        $blueprint = new Blueprint($table);
        $callback($blueprint);
        $queries = $blueprint->toAlterSql();
        foreach ($queries as $query) {
            Database::connect()->exec($query);
        }
    }

    public static function drop(string $table): void
    {
        $db = Database::connect();
        $stmt = $db->prepare("DROP TABLE IF EXISTS `{$table}`");
        $stmt->execute();
    }

    public function hasTable(string $table): bool
    {
        $stmt = $this->db->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$table]);
        return $stmt->rowCount() > 0;
    }
}
