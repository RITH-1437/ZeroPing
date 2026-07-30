<?php

declare(strict_types=1);

namespace App\Core\Queue;

/**
 * Value object holding configuration options for the queue worker.
 *
 * Encapsulates all tunable parameters that control how the worker
 * processes jobs, including retry limits, timeouts, sleep intervals,
 * and memory constraints.
 *
 * @example
 * ```php
 * $options = new WorkerOptions(
 *     maxTries: 3,
 *     timeout: 120,
 *     sleep: 5,
 *     memoryLimit: 256,
 *     delay: 0,
 *     stopOnEmpty: false
 * );
 * ```
 */
final class WorkerOptions
{
    /**
     * Create a new worker options instance.
     *
     * @param int $maxTries The maximum number of times a job may be attempted (default: 1).
     * @param int $timeout The number of seconds a job can run before timing out (default: 60).
     * @param int $sleep The number of seconds to sleep when no job is available (default: 3).
     * @param int $memoryLimit The maximum memory in MB before the worker restarts (default: 128).
     * @param int $delay The number of seconds to delay between processing jobs (default: 0).
     * @param bool $stopOnEmpty Whether to stop the worker when the queue is empty (default: false).
     */
    public function __construct(
        public readonly int $maxTries = 1,
        public readonly int $timeout = 60,
        public readonly int $sleep = 3,
        public readonly int $memoryLimit = 128,
        public readonly int $delay = 0,
        public readonly bool $stopOnEmpty = false,
    ) {
    }
}
