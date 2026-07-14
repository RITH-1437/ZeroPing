<?php

namespace App\Core\Console\Commands;

use App\Core\Application\App;
use App\Core\Console\Command;
use App\Core\Scheduling\ScheduleManager;

class ScheduleListCommand extends Command
{
    protected string $signature = 'schedule:list';

    protected string $description = 'List the scheduled commands';

    public function handle(): void
    {
        $schedule = App::container()->make(ScheduleManager::class)->schedule();

        $this->info('Scheduled Commands:');

        foreach ($schedule->events() as $event) {
            $this->info("- {$event->expression} {$event->command}");
        }
    }
}
