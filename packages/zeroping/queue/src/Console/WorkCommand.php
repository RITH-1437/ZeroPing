<?php

namespace Zeroping\Queue\Console;

use Zeroping\Queue\Contracts\QueueManager;
use Zeroping\Support\Console\Command;

/**
 * php zero queue:work
 *
 * Processes the default (or named) queue. Scaffold: loops once; a real worker
 * would long-poll and respect a timeout / signal.
 */
class WorkCommand extends Command
{
    public function name(): string
    {
        return 'queue:work';
    }

    public function handle(): void
    {
        /** @var QueueManager $manager */
        $manager = app(\Zeroping\Queue\Contracts\QueueManager::class);
        $queue   = $manager->connection($this->argument(0));

        $this->info('Processing queue (scaffold worker)...');

        $job = $queue->pop();
        if ($job === null) {
            $this->comment('No jobs to process.');
            return;
        }

        try {
            $job->handle();
            $this->success('Job processed.');
        } catch (\Throwable $e) {
            $job->failed($e);
            $this->error($e->getMessage());
        }
    }
}
