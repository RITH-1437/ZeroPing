<?php

declare(strict_types=1);

namespace App\Core\Scheduling;

/**
 * Manages the application's task schedule.
 *
 * Provides access to the shared {@see Schedule} instance and acts as
 * a proxy for schedule-related operations. Typically resolved from the
 * service container as a singleton.
 *
 * @example
 * ```php
 * $manager = new ScheduleManager();
 * $manager->schedule()->command('cache:clear')->daily();
 * $manager->schedule()->call(fn() => doWork())->hourly();
 * ```
 */
class ScheduleManager
{
    /**
     * The schedule instance.
     *
     * @var Schedule
     */
    protected Schedule $schedule;

    /**
     * Create a new schedule manager instance.
     */
    public function __construct()
    {
        $this->schedule = new Schedule();
    }

    /**
     * Get the schedule instance.
     *
     * @return Schedule
     */
    public function schedule(): Schedule
    {
        return $this->schedule;
    }

    /**
     * Proxy method calls to the underlying schedule instance.
     *
     * @param string $method The method name.
     * @param array<int, mixed> $parameters The method arguments.
     * @return mixed
     */
    public function __call(string $method, array $parameters): mixed
    {
        return $this->schedule()->{$method}(...$parameters);
    }
}
