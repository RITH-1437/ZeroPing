<?php

namespace App\Core\Testing\Database;

use App\Core\Database\MigrationRunner;

trait RefreshDatabase
{
    public function refreshDatabase(): void
    {
        (new MigrationRunner())->fresh();
    }
}
