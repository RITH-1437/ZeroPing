<?php

namespace App\Core\Console\Commands;

use App\Core\Console\Command;
use App\Core\Database\MigrationRunner;

class MigrateStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected string $signature = 'migrate:status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected string $description = 'Show the status of each migration';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $migrationRunner = new MigrationRunner();
        $migrations = $migrationRunner->getMigrations();
        $ranMigrations = $migrationRunner->getRanMigrations();

        $this->info('Migration Status');
        $this->info('----------------');

        foreach ($migrations as $migration) {
            $status = in_array($migration, $ranMigrations) ? 'Ran' : 'Pending';
            $this->info("{$migration}: {$status}");
        }
    }
}
