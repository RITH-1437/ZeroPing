<?php

namespace App\Core\Console\Commands;

use App\Core\Console\Command;
use App\Core\Queue\Dispatcher;
use App\Jobs\TestJob;

class QueueTestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected string $signature = 'queue:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected string $description = 'Test the queue system';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->info('Testing queue system...');

        Dispatcher::dispatch(new TestJob());

        $this->info('Queue test completed successfully!');
    }
}
