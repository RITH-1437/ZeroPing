<?php

declare(strict_types=1);

namespace App\Core\Scheduling;

use App\Core\Queue\Job;

/**
 * Defines the schedule of recurring tasks for the application.
 *
 * Provides a fluent API for registering commands, callbacks, and jobs
 * that should be executed on a recurring basis. Each registration
 * returns the appropriate Event subclass for further configuration.
 *
 * @example
 * ```php
 * $schedule = new Schedule();
 *
 * $schedule->command('cache:clear')->daily();
 * $schedule->call(fn() => cleanup())->hourly();
 * $schedule->job(new ReportJob())->weeklyOn(1, '08:00');
 *
 * foreach ($schedule->events() as $event) {
 *     if ($event->isDue()) { $event->run(); }
 * }
 * ```
 *
 * @since 1.0.0
 * @author Rin Nairith
 * @link https://zero-ping.duckdns.org/docs/scheduler
 */
class Schedule
{
    /**
     * All registered scheduled events.
     *
     * @var array<int, Event>
     */
    protected array $events = [];

    /**
     * Register a shell command to be scheduled.
     *
     * @param string $command The shell command to schedule.
     * @return CommandEvent The event instance for further configuration.
     */
    public function command(string $command): CommandEvent
    {
        $event = new CommandEvent($command);
        $this->events[] = $event;

        return $event;
    }

    /**
     * Register a queue job to be dispatched on schedule.
     *
     * @param Job $job The job instance to dispatch when due.
     * @return JobEvent The event instance for further configuration.
     */
    public function job(Job $job): JobEvent
    {
        $event = new JobEvent($job);
        $this->events[] = $event;

        return $event;
    }

    /**
     * Register a callback to be invoked on schedule.
     *
     * @param callable $callback The callback to execute when due.
     * @param string $description Optional description for the task.
     * @return CallbackEvent The event instance for further configuration.
     */
    public function call(callable $callback, string $description = 'Callback'): CallbackEvent
    {
        $event = new CallbackEvent($callback, $description);
        $this->events[] = $event;

        return $event;
    }

    /**
     * Get all registered scheduled events.
     *
     * @return array<int, Event> The array of scheduled events.
     */
    public function events(): array
    {
        return $this->events;
    }

    /**
     * Get all events that are currently due.
     *
     * Filters the registered events to only those whose cron expression
     * matches the current time and whose filter conditions pass.
     *
     * @return array<int, Event> The due events.
     */
    public function dueEvents(): array
    {
        return array_values(array_filter(
            $this->events,
            fn(Event $event): bool => $event->isDue() && $event->filtersPass()
        ));
    }
}
