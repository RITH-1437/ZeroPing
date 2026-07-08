<?php

namespace App\Core\Console\Commands;

use App\Core\Console\Command;
use App\Core\Queue\FailedJob;
use App\Core\Queue\Dispatcher;

class QueueRetryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected string $signature = 'queue:retry';

    /**
     * The console command description.
     *
     * @var string
     */
    protected string $description = 'Retry a failed queue job';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(string $id): void
    {
        if (empty($id)) {
            $this->error('Please provide a job ID. Usage: php zero queue:retry <id>');
            return;
        }

        $job = FailedJob::find((int) $id);

        if (!$job) {
            $this->error('Failed job not found.');
            return;
        }

        $payload = unserialize($job->payload);

        Dispatcher::dispatch($payload);

        $job->delete();

        $this->info('Failed job retried successfully.');
    }
}
