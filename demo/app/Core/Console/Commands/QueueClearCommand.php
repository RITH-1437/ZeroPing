<?php

namespace App\Core\Console\Commands;

use App\Core\Console\Command;
use App\Core\Database\Database;

class QueueClearCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected string $signature = 'queue:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected string $description = 'Delete all of the jobs from the specified queue';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $db = Database::connect();
        $db->prepare("DELETE FROM jobs")->execute();

        $this->info('All jobs have been deleted.');
    }
}
