<?php

namespace App\Core\Scheduling;

use App\Core\Application\App;
use App\Core\Support\Log;

class Scheduler
{
    protected Schedule $schedule;

    public function __construct()
    {
        $this->schedule = App::container()->make(ScheduleManager::class)->schedule();
    }

    public function run(): void
    {
        foreach ($this->schedule->events() as $event) {
            if ($event->isDue() && $event->filtersPass()) {
                $this->runEvent($event);
            }
        }
    }

    protected function runEvent(Event $event): void
    {
        if ($event->withoutOverlapping) {
            if ($this->mutexExists($event)) {
                return;
            }

            $this->createMutex($event);
        }

        Log::info("Running scheduled event: " . $event->command);

        $event->run();

        if ($event->withoutOverlapping) {
            $this->removeMutex($event);
        }
    }

    protected function mutexExists(Event $event): bool
    {
        // This is a simplified implementation. A real implementation would
        // use a cache driver.
        return false;
    }

    protected function createMutex(Event $event): void
    {
        // This is a simplified implementation. A real implementation would
        // use a cache driver.
    }

    protected function removeMutex(Event $event): void
    {
        // This is a simplified implementation. A real implementation would
        // use a cache driver.
    }
}
