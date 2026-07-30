<?php

declare(strict_types=1);

namespace App\Core\Scheduling;

use App\Core\Queue\Dispatcher;
use App\Core\Queue\Job;

/**
 * A scheduled task that dispatches a queue job.
 *
 * When due, the job is dispatched to the queue for background processing
 * rather than being executed inline. This allows scheduled tasks to
 * leverage the queue's retry, timeout, and failure handling mechanisms.
 *
 * @example
 * ```php
 * $schedule->job(new GenerateReportJob($params))
 *     ->dailyAt('06:00')
 *     ->withoutOverlapping();
 * ```
 */
class JobEvent extends Event
{
    /**
     * The queue job instance to dispatch.
     *
     * @var Job
     */
    protected Job $job;

    /**
     * Create a new job event instance.
     *
     * @param Job $job The job to dispatch when the task is due.
     */
    public function __construct(Job $job)
    {
        parent::__construct(get_class($job));
        $this->job = $job;
    }

    /**
     * Execute the scheduled task by dispatching the job to the queue.
     *
     * @return void
     */
    public function run(): void
    {
        Dispatcher::dispatch($this->job);
    }

    /**
     * Get the queue job instance.
     *
     * @return Job
     */
    public function getJob(): Job
    {
        return $this->job;
    }
}
