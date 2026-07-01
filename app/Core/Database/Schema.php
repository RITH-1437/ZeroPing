<?php

namespace App\Core\Database;

class Schema
{
    public static function create(
        string $table,
        callable $callback
    ): void {

        $blueprint = new Blueprint($table);

        $callback($blueprint);

        $sql = "
            CREATE TABLE IF NOT EXISTS {$table} (
                {$blueprint->build()}
            )
        ";

        Database::connect()->exec($sql);
    }

    public static function drop(string $table): void
    {
        Database::connect()->exec(
            "DROP TABLE IF EXISTS {$table}"
        );
    }
}