<?php

namespace App\Core\Console\Commands;

use App\Core\Console\Command;
use App\Core\Queue\Worker;
use App\Core\Queue\QueueManager;

class QueueWorkCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected string $signature = 'queue:work';

    /**
     * The console command description.
     *
     * @var string
     */
    protected string $description = 'Start processing jobs on the queue as a daemon';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $worker = new Worker(new QueueManager());
        $worker->run(
            $this->option('connection') ?? 'sync',
            $this->option('queue'),
            (int) ($this->option('delay') ?? 0),
            (int) ($this->option('sleep') ?? 3),
            (int) ($this->option('tries') ?? 1)
        );
    }
}
