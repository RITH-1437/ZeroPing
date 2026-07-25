<?php

namespace App\Core\Console\Commands;

use App\Core\Console\Command;
use App\Core\Cache\Cache;

class ScheduleClearCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected string $signature = 'schedule:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected string $description = 'Remove the scheduler mutex files';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        Cache::forget('framework/schedule-*');

        $this->info('Scheduler mutex files cleared.');
    }
}
