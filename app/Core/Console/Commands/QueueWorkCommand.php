<?php

namespace App\Core\Console\Commands;

use App\Core\Console\Command;
use App\Core\Queue\Worker;

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
        $worker = new Worker();
        $worker->run(
            $this->option('connection'),
            $this->option('queue'),
            $this->option('delay'),
            $this->option('sleep'),
            $this->option('tries')
        );
    }
}
