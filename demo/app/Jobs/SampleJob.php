<?php

namespace App\Jobs;

use App\Core\Queue\Job;

/**
 * SampleJob — illustrative queued job.
 *
 * Jobs encapsulate units of work for the queue. Configure retries/timeout and
 * implement handle(). Dispatch with `queue(new SampleJob(...))`.
 */
class SampleJob extends Job
{
    public int $tries = 3;
    public int $timeout = 120;

    public function handle(): void
    {
        // TODO: implement SampleJob logic.
    }
}
