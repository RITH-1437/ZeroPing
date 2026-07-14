<?php

namespace App\Core\Testing\Database;

use App\Core\Database\Database;

trait Transaction
{
    public function beginDatabaseTransaction(): void
    {
        Database::connection()->beginTransaction();
    }

    public function rollbackDatabaseTransaction(): void
    {
        Database::connection()->rollBack();
    }
}
