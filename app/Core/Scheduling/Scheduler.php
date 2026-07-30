<?php

declare(strict_types=1);

namespace App\Core\Scheduling;

use App\Core\Application\App;
use App\Core\Cache\CacheRepository;
use App\Core\Support\Log;

/**
 * Executes due scheduled tasks.
 *
 * The scheduler is invoked periodically (typically every minute via cron)
 * and runs all tasks that are currently due. It handles mutex-based overlap
 * prevention, logging, and error handling for each task.
 *
 * @example
 * ```php
 * // Typically invoked by a console command:
 * // * * * * * php zero schedule:run
 *
 * $scheduler = new Scheduler();
 * $scheduler->run(); // Runs all due tasks
 * ```
 */
class Scheduler
{
    /**
     * The schedule containing all registered tasks.
     *
     * @var Schedule
     */
    protected Schedule $schedule;

    /**
     * The mutex implementation for overlap prevention.
     *
     * @var Mutex|null
     */
    protected ?Mutex $mutex;

    /**
     * Create a new scheduler instance.
     *
     * @param Mutex|null $mutex Optional mutex implementation. Falls back to cache-based mutexing.
     */
    public function __construct(?Mutex $mutex = null)
    {
        $this->schedule = App::container()->make(ScheduleManager::class)->schedule();
        $this->mutex = $mutex;
    }

    /**
     * Run all due scheduled tasks.
     *
     * Iterates through all registered events, checks if they are due
     * and pass their filter conditions, then executes them. Overlap
     * prevention is enforced via mutexes when configured.
     *
     * @return array<int, Event> The events that were executed.
     */
    public function run(): array
    {
        $executedEvents = [];

        foreach ($this->schedule->events() as $event) {
            if (!$event->isDue() || !$event->filtersPass()) {
                continue;
            }

            $this->runEvent($event);
            $executedEvents[] = $event;
        }

        return $executedEvents;
    }

    /**
     * Execute a single scheduled event.
     *
     * Handles overlap prevention via mutexes, logs execution, and
     * catches/logs any exceptions thrown by the event.
     *
     * @param Event $event The event to execute.
     * @return void
     */
    protected function runEvent(Event $event): void
    {
        if ($event->withoutOverlapping) {
            if ($this->mutexExists($event)) {
                Log::info(sprintf(
                    'Skipping overlapping scheduled task: %s',
                    $event->description ?? $event->command
                ));
                return;
            }

            $this->createMutex($event);
        }

        $description = $event->description ?? $event->command;
        Log::info(sprintf('Running scheduled task: %s', $description));

        try {
            $event->run();
            Log::info(sprintf('Completed scheduled task: %s', $description));
        } catch (\Throwable $e) {
            Log::error(sprintf(
                'Scheduled task [%s] failed: %s',
                $description,
                $e->getMessage()
            ));
        } finally {
            if ($event->withoutOverlapping) {
                $this->removeMutex($event);
            }
        }
    }

    /**
     * Generate the mutex/cache key name for an event.
     *
     * @param Event $event The scheduled event.
     * @return string The mutex key.
     */
    protected function mutexName(Event $event): string
    {
        return 'scheduler-mutex-' . md5($event->getMutexName());
    }

    /**
     * Check if a mutex currently exists for the given event.
     *
     * @param Event $event The scheduled event.
     * @return bool True if the mutex exists (event is already running).
     */
    protected function mutexExists(Event $event): bool
    {
        if ($this->mutex !== null) {
            return $this->mutex->exists($event);
        }

        return App::container()->make(CacheRepository::class)->has(
            $this->mutexName($event)
        );
    }

    /**
     * Create a mutex for the given event.
     *
     * @param Event $event The scheduled event.
     * @return void
     */
    protected function createMutex(Event $event): void
    {
        if ($this->mutex !== null) {
            $this->mutex->create($event);
            return;
        }

        App::container()->make(CacheRepository::class)->put(
            $this->mutexName($event),
            true,
            $event->expiresAt * 60 // Convert minutes to seconds
        );
    }

    /**
     * Remove the mutex for the given event.
     *
     * @param Event $event The scheduled event.
     * @return void
     */
    protected function removeMutex(Event $event): void
    {
        if ($this->mutex !== null) {
            $this->mutex->forget($event);
            return;
        }

        App::container()->make(CacheRepository::class)->forget(
            $this->mutexName($event)
        );
    }

    /**
     * Get the underlying schedule instance.
     *
     * @return Schedule
     */
    public function getSchedule(): Schedule
    {
        return $this->schedule;
    }
}
