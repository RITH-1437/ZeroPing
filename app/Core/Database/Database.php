<?php

namespace App\Core\Database;

use PDO;

class Database
{
    private static ?PDO $connection = null;
    private static array $log = [];

    public static function connect(): PDO
    {
        if (self::$connection === null) {

            $dsn = sprintf(
                "mysql:host=%s;dbname=%s;charset=%s",
                DB_HOST,
                DB_NAME,
                DB_CHARSET
            );

            self::$connection = new PDO(
                $dsn,
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );

            self::$connection->setAttribute(PDO::ATTR_STATEMENT_CLASS, [ProfiledStatement::class, [self::$connection]]);
        }

        return self::$connection;
    }

    public static function getSchemaBuilder(): Schema
    {
        return new Schema(self::connect());
    }

    public static function addLog(string $query, array $bindings, float $time): void
    {
        self::$log[] = [
            'query' => $query,
            'bindings' => $bindings,
            'time' => $time,
        ];
    }

    public static function getLog(): array
    {
        return self::$log;
    }
}
