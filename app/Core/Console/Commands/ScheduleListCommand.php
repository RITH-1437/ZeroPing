<?php

namespace App\Core\Console\Commands;

use App\Core\Console\Command;
use App\Core\Scheduling\ScheduleManager;

class ScheduleListCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected string $signature = 'schedule:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected string $description = 'List the scheduled commands';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $schedule = (new ScheduleManager())->schedule();

        $this->info('Scheduled Commands:');

        foreach ($schedule->events() as $event) {
            $this->info("- {$event->expression} {$event->command}");
        }
    }
}
