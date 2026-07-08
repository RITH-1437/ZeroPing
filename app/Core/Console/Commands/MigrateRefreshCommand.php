<?php

namespace App\Core\Console\Commands;

use App\Core\Console\Command;
use App\Core\Database\MigrationRunner;

class MigrateRefreshCommand extends Command
{
    protected string $signature = 'migrate:refresh';

    protected string $description = 'Rollback all migrations then re-run them';

    public function handle(): void
    {
        $runner = new MigrationRunner();
        $runner->refresh();
    }
}
