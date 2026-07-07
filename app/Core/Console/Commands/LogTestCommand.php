<?php

namespace App\Core\Console\Commands;

use App\Core\Console\Command;
use App\Core\Support\Log;

class LogTestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected string $signature = 'log:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected string $description = 'Test the logger';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->info('Testing logger...');

        Log::info('This is an info message.');
        Log::warning('This is a warning message.');
        Log::error('This is an error message.');

        $this->info('Logger test completed successfully!');
    }
}
