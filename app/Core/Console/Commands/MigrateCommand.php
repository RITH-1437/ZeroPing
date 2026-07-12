<?php

namespace App\Core\Console\Commands;

use App\Core\Console\Command;
use App\Core\Database\MigrationRunner;

class MigrateCommand extends Command
{
    /**
     * The console command description.
     *
     * @var string
     */
    protected string $signature = 'migrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected string $description = 'Run database migrations';

    public function handle(): void
    {
        $runner = new MigrationRunner();

        $pending = $runner->pendingMigrationFiles();

        if ($pending === []) {
            $this->warn('Nothing to migrate.');
            return;
        }

        $this->title('Running Migrations');

        $batch = $runner->nextBatch();

        $this->progress(count($pending), function (int $index, int $total) use ($runner, $pending, $batch) {
            $runner->runUp($pending[$index], $batch);
        }, 'Migrating');

        $this->success('Migration completed successfully (' . count($pending) . ' file(s)).');
    }
}
