<?php

namespace App\Core\Console\Commands;

use App\Core\Console\Command;
use App\Core\Queue\FailedJob;

class QueueFailedCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected string $signature = 'queue:failed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected string $description = 'List all of the failed queue jobs';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $jobs = FailedJob::all();

        if (count($jobs) === 0) {
            $this->info('No failed jobs!');
            return;
        }

        $this->info('Failed Jobs:');
        foreach ($jobs as $job) {
            $this->info("ID: {$job->id} | Connection: {$job->connection} | Queue: {$job->queue} | Failed At: {$job->failed_at}");
        }
    }
}
