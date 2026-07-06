<?php

namespace App\Core\Database;

use PDO;

class Database
{
    private static ?PDO $connection = null;

    public static function connect(): PDO
    {
        if (self::$connection === null) {

            $dsn = sprintf(
                "mysql:host=%s;dbname=%s;charset=%s",
                config('database.host'),
                config('database.database'),
                config('database.charset'),
            );

            self::$connection = new PDO(
                $dsn,
                config('database.username'),
                config('database.password'),
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        }

        return self::$connection;
    }
}