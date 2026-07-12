<?php

namespace App\Core\Console\Commands;

use App\Core\Console\Command;
use App\Core\Database\MigrationRunner;

class MigrateResetCommand extends Command
{
    protected string $signature = 'migrate:reset';
    protected string $description = 'Rollback all database migrations';

    public function handle(): void
    {
        $this->title('Resetting Migrations');

        $migrationRunner = new MigrationRunner();
        $migrationRunner->reset();

        $this->success('All migrations rolled back successfully.');
    }
}