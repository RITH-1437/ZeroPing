<?php

namespace App\Core\Console\Commands;

use App\Core\Console\Command;
use App\Core\Database\MigrationRunner;

class MigrateFreshCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected string $signature = 'migrate:fresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected string $description = 'Drop all tables and re-run all migrations';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $migrationRunner = new MigrationRunner();
        $migrationRunner->fresh();

        $this->info('All tables dropped and migrations re-ran successfully.');
    }
}
