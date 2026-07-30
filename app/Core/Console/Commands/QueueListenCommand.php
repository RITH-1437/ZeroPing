<?php

namespace App\Core\Console\Commands;

use App\Core\Console\Command;
use App\Core\Queue\Worker;
use App\Core\Queue\QueueManager;

class QueueListenCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected string $signature = 'queue:listen';

    /**
     * The console command description.
     *
     * @var string
     */
    protected string $description = 'Listen to a given queue';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $worker = new Worker(new QueueManager());
        $worker->run(
            (string) ($this->option('connection') ?? 'sync'),
            $this->option('queue') !== null ? (string) $this->option('queue') : null,
            new \App\Core\Queue\WorkerOptions(
                sleep: (int) ($this->option('sleep') ?? 3),
                maxTries: (int) ($this->option('tries') ?? 1),
            )
        );
    }
}
