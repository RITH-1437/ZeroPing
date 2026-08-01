<?php

declare(strict_types=1);

namespace App\Core\Queue;

use App\Core\Application\App;

/**
 * Static facade for dispatching jobs to the queue.
 *
 * Provides convenient static methods for pushing jobs onto the queue
 * without needing to manually resolve the QueueManager from the container.
 *
 * @example
 * ```php
 * // Dispatch to the job's configured queue
 * Dispatcher::dispatch(new SendEmailJob($user));
 *
 * // Dispatch synchronously (process immediately)
 * Dispatcher::dispatchSync(new SendEmailJob($user));
 *
 * // Dispatch with a delay
 * Dispatcher::dispatchLater(60, new SendEmailJob($user));
 * ```
 */
class Dispatcher
{
    /**
     * Dispatch a job to its configured queue connection.
     *
     * The job will be pushed to the connection specified by its `queue` property.
     * If no queue is configured on the job, the default connection is used.
     *
     * @param Job $job The job to dispatch.
     * @return void
     */
    public static function dispatch(Job $job): void
    {
        static::getManager()
            ->connection($job->getQueue())
            ->push($job);
    }

    /**
     * Dispatch a job synchronously (process immediately in the current process).
     *
     * Useful for jobs that should be executed right away without going
     * through the queue, such as during local development or testing.
     *
     * @param Job $job The job to dispatch synchronously.
     * @return void
     */
    public static function dispatchSync(Job $job): void
    {
        static::getManager()
            ->connection('sync')
            ->push($job);
    }

    /**
     * Dispatch a job with a delay before it becomes available for processing.
     *
     * @param int $delay The number of seconds to delay the job.
     * @param Job $job The job to dispatch.
     * @return void
     */
    public static function dispatchLater(int $delay, Job $job): void
    {
        static::getManager()
            ->connection($job->getQueue())
            ->later($delay, $job);
    }

    /**
     * Resolve the QueueManager instance from the application container.
     *
     * @return QueueManager
     */
    protected static function getManager(): QueueManager
    {
        /** @var QueueManager */
        return App::container()->make(QueueManager::class);
    }
}
