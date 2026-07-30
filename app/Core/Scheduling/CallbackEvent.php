<?php

declare(strict_types=1);

namespace App\Core\Scheduling;

/**
 * A scheduled task that invokes a PHP callback.
 *
 * Useful for scheduling inline logic without creating a separate command
 * or job class. The callback is executed synchronously when the task is due.
 *
 * @example
 * ```php
 * $schedule->call(function () {
 *     // Clean up temporary files...
 * })->hourly();
 *
 * $schedule->call([$service, 'cleanup'])->daily();
 * ```
 */
class CallbackEvent extends Event
{
    /**
     * The callback to invoke when the task runs.
     *
     * @var callable
     */
    protected $callback;

    /**
     * The result returned by the callback after execution.
     *
     * @var mixed
     */
    protected mixed $result = null;

    /**
     * Create a new callback event instance.
     *
     * @param callable $callback The callback to invoke.
     * @param string $description Optional description for the task.
     */
    public function __construct(callable $callback, string $description = 'Callback')
    {
        parent::__construct($description);
        $this->callback = $callback;
    }

    /**
     * Execute the scheduled callback.
     *
     * The callback is invoked with no arguments. Any return value is
     * stored and accessible via `getResult()`.
     *
     * @return void
     */
    public function run(): void
    {
        $this->result = call_user_func($this->callback);
    }

    /**
     * Get the result returned by the callback after execution.
     *
     * @return mixed The callback's return value, or null if not yet executed.
     */
    public function getResult(): mixed
    {
        return $this->result;
    }

    /**
     * Get the mutex name for the callback event.
     *
     * Since callbacks don't have a stable string representation,
     * we use the expression + description for the hash.
     *
     * @return string
     */
    public function getMutexName(): string
    {
        return $this->mutexName ?: 'framework/schedule-' . sha1($this->expression . $this->command);
    }
}
