<?php

namespace App\Core\Scheduling;

use App\Core\Application\App;
use App\Core\Cache\CacheRepository;
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

    protected function mutexName(Event $event): string
    {
        return 'scheduler-mutex-' . md5($event->command);
    }

    protected function mutexExists(Event $event): bool
    {
        return App::container()->make(CacheRepository::class)->has(
            $this->mutexName($event)
        );
    }

    protected function createMutex(Event $event): void
    {
        App::container()->make(CacheRepository::class)->put(
            $this->mutexName($event),
            true,
            $event->expiresAt
        );
    }

    protected function removeMutex(Event $event): void
    {
        App::container()->make(CacheRepository::class)->forget(
            $this->mutexName($event)
        );
    }
}
