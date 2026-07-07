<?php

namespace App\Core\Console\Commands;

use App\Core\Console\Command;
use App\Core\Database\MigrationRunner;

class MigrateRollbackCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected string $signature = 'migrate:rollback';

    /**
     * The console command description.
     *
     * @var string
     */
    protected string $description = 'Rollback the last database migration';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $migrationRunner = new MigrationRunner();
        $migrationRunner->rollback();

        $this->info('Migration rolled back successfully.');
    }
}
