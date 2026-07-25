<?php

namespace App\Core\Console\Commands;

use App\Core\Console\Command;
use App\Core\Scheduling\Scheduler;

class ScheduleRunCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected string $signature = 'schedule:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected string $description = 'Run the scheduled commands';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $scheduler = new Scheduler();
        $scheduler->run();
    }
}
