<?php

declare(strict_types=1);

namespace App\Core\Queue;

use App\Core\Support\Log;

/**
 * Queue worker that processes jobs from a queue connection.
 *
 * The worker continuously polls for new jobs and processes them,
 * handling retries, timeouts, and failure logging. Configuration
 * options control retry behavior, sleep intervals, and memory limits.
 *
 * @example
 * ```php
 * $worker = new Worker($queueManager);
 * $worker->run('database', 'default', new WorkerOptions(
 *     maxTries: 3,
 *     timeout: 120,
 *     sleep: 5,
 *     memoryLimit: 128
 * ));
 * ```
 */
class Worker
{
    /**
     * The queue manager instance.
     *
     * @var QueueManager
     */
    protected QueueManager $manager;

    /**
     * Indicates if the worker should stop processing.
     *
     * @var bool
     */
    protected bool $shouldQuit = false;

    /**
     * Indicates if the worker is paused.
     *
     * @var bool
     */
    protected bool $paused = false;

    /**
     * Create a new queue worker instance.
     *
     * @param QueueManager $manager The queue manager for resolving connections.
     */
    public function __construct(QueueManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Listen to the given queue connection and process jobs.
     *
     * This is the main worker loop. It continuously polls the specified
     * queue for jobs and processes them according to the provided options.
     * The loop respects memory limits, pause/quit signals, and sleep intervals.
     *
     * @param string $connection The queue connection name (e.g., 'database', 'sync').
     * @param string|null $queue The specific queue name to listen on, or null for default.
     * @param WorkerOptions $options Configuration options for the worker.
     * @return void
     */
    public function run(string $connection, ?string $queue = null, ?WorkerOptions $options = null): void
    {
        $options = $options ?? new WorkerOptions();

        while (!$this->shouldQuit) {
            if ($this->paused) {
                $this->sleep($options->sleep);
                continue;
            }

            $job = $this->getNextJob($connection, $queue);

            if ($job !== null) {
                $this->process($connection, $job, $options);
            } else {
                $this->sleep($options->sleep);
            }

            if ($this->memoryExceeded($options->memoryLimit)) {
                $this->stop();
            }
        }
    }

    /**
     * Process a single job from the queue.
     *
     * Runs the job within a timeout constraint, handles success/failure
     * logging, and manages retries or failure recording as appropriate.
     *
     * @param string $connection The queue connection name.
     * @param Job $job The job instance to process.
     * @param WorkerOptions $options The worker options for retry/timeout configuration.
     * @return void
     */
    public function process(string $connection, Job $job, WorkerOptions $options): void
    {
        $maxTries = $this->resolveMaxTries($job, $options);
        $timeout = $this->resolveTimeout($job, $options);

        try {
            $this->raiseBeforeJobEvent($connection, $job);
            $this->executeJob($job, $timeout);
            $this->raiseAfterJobEvent($connection, $job);
            $this->manager->connection($connection)->delete($job);
        } catch (\Throwable $e) {
            $this->handleJobException($connection, $job, $e, $maxTries);
        }
    }

    /**
     * Execute the job with an optional timeout.
     *
     * If the timeout is greater than zero and the pcntl extension is available,
     * the job execution is wrapped in a SIGALRM-based timeout. Otherwise,
     * the job runs without a hard timeout constraint.
     *
     * @param Job $job The job to execute.
     * @param int $timeout The timeout in seconds (0 = no timeout).
     * @return void
     *
     * @throws \RuntimeException If the job exceeds the configured timeout.
     */
    protected function executeJob(Job $job, int $timeout): void
    {
        if ($timeout > 0 && function_exists('pcntl_alarm')) {
            pcntl_alarm($timeout);

            $previousHandler = pcntl_signal(SIGALRM, function () use ($job, $timeout): void {
                throw new \RuntimeException(sprintf(
                    'Job [%s] has timed out after %d seconds.',
                    get_class($job),
                    $timeout
                ));
            });

            try {
                $job->handle();
            } finally {
                pcntl_alarm(0);
                if ($previousHandler !== false) {
                    pcntl_signal(SIGALRM, $previousHandler);
                }
            }
        } else {
            $job->handle();
        }
    }

    /**
     * Resolve the maximum number of retry attempts for a job.
     *
     * The job's own `tries` property takes precedence over the worker-level
     * `maxTries` option, allowing per-job retry customization.
     *
     * @param Job $job The job being processed.
     * @param WorkerOptions $options The worker options.
     * @return int The resolved maximum tries.
     */
    protected function resolveMaxTries(Job $job, WorkerOptions $options): int
    {
        return $job->getMaxTries() > 0 ? $job->getMaxTries() : $options->maxTries;
    }

    /**
     * Resolve the timeout for a job.
     *
     * The job's own `timeout` property takes precedence over the worker-level
     * `timeout` option, allowing per-job timeout customization.
     *
     * @param Job $job The job being processed.
     * @param WorkerOptions $options The worker options.
     * @return int The resolved timeout in seconds.
     */
    protected function resolveTimeout(Job $job, WorkerOptions $options): int
    {
        return $job->getTimeout() > 0 ? $job->getTimeout() : $options->timeout;
    }

    /**
     * Get the next job from the queue.
     *
     * @param string $connection The queue connection name.
     * @param string|null $queue The queue name to pop from.
     * @return Job|null The next available job, or null if the queue is empty.
     */
    protected function getNextJob(string $connection, ?string $queue = null): ?Job
    {
        return $this->manager->connection($connection)->pop($queue);
    }

    /**
     * Handle an exception that occurred during job processing.
     *
     * If the job has remaining retry attempts, it is released back to
     * the queue with its configured delay. Otherwise, the job's `failed()`
     * method is called and it is logged as a failed job.
     *
     * @param string $connection The queue connection name.
     * @param Job $job The job that threw the exception.
     * @param \Throwable $e The exception that was thrown.
     * @param int $maxTries The maximum allowed attempts.
     * @return void
     */
    protected function handleJobException(string $connection, Job $job, \Throwable $e, int $maxTries): void
    {
        if ($job->attempts() < $maxTries) {
            $this->raiseJobRetryEvent($connection, $job, $e);
            $this->manager->connection($connection)->release($job, $job->getDelay());
        } else {
            $this->raiseJobFailedEvent($connection, $job, $e);
            $job->failed($e);
            $this->logFailedJob($connection, $job, $e);
            $this->manager->connection($connection)->delete($job);
        }
    }

    /**
     * Log a failed job to the failed_jobs table.
     *
     * @param string $connection The queue connection name.
     * @param Job $job The failed job instance.
     * @param \Throwable $e The exception that caused the failure.
     * @return void
     */
    protected function logFailedJob(string $connection, Job $job, \Throwable $e): void
    {
        FailedJob::create([
            'connection' => $connection,
            'queue' => $job->getQueue(),
            'payload' => $job->toPayload(),
            'exception' => (string) $e,
        ]);
    }

    /**
     * Determine if the memory limit has been exceeded.
     *
     * @param int $memoryLimit The memory limit in megabytes.
     * @return bool True if memory usage exceeds the limit.
     */
    protected function memoryExceeded(int $memoryLimit): bool
    {
        return (memory_get_usage(true) / 1024 / 1024) >= $memoryLimit;
    }

    /**
     * Sleep the worker for the given number of seconds.
     *
     * @param int $seconds The number of seconds to sleep.
     * @return void
     */
    protected function sleep(int $seconds): void
    {
        if ($seconds > 0) {
            sleep($seconds);
        }
    }

    /**
     * Stop the worker loop after the current job completes.
     *
     * @return void
     */
    public function stop(): void
    {
        $this->shouldQuit = true;
    }

    /**
     * Pause the worker (it will sleep until resumed).
     *
     * @return void
     */
    public function pause(): void
    {
        $this->paused = true;
    }

    /**
     * Resume a paused worker.
     *
     * @return void
     */
    public function resume(): void
    {
        $this->paused = false;
    }

    /**
     * Log event before a job is processed.
     *
     * @param string $connection The queue connection name.
     * @param Job $job The job about to be processed.
     * @return void
     */
    protected function raiseBeforeJobEvent(string $connection, Job $job): void
    {
        Log::info(sprintf('Processing job [%s] on connection [%s].', get_class($job), $connection));
    }

    /**
     * Log event after a job is successfully processed.
     *
     * @param string $connection The queue connection name.
     * @param Job $job The job that was processed.
     * @return void
     */
    protected function raiseAfterJobEvent(string $connection, Job $job): void
    {
        Log::info(sprintf('Processed job [%s] on connection [%s].', get_class($job), $connection));
    }

    /**
     * Log event when a job fails permanently.
     *
     * @param string $connection The queue connection name.
     * @param Job $job The failed job.
     * @param \Throwable $e The exception that caused the failure.
     * @return void
     */
    protected function raiseJobFailedEvent(string $connection, Job $job, \Throwable $e): void
    {
        Log::error(sprintf('Job [%s] failed on connection [%s]: %s', get_class($job), $connection, $e->getMessage()));
    }

    /**
     * Log event when a job is being retried.
     *
     * @param string $connection The queue connection name.
     * @param Job $job The job being retried.
     * @param \Throwable $e The exception that triggered the retry.
     * @return void
     */
    protected function raiseJobRetryEvent(string $connection, Job $job, \Throwable $e): void
    {
        Log::info(sprintf(
            'Retrying job [%s] on connection [%s] (attempt %d): %s',
            get_class($job),
            $connection,
            $job->attempts() + 1,
            $e->getMessage()
        ));
    }
}
