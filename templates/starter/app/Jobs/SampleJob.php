<?php

namespace App\Jobs;

use App\Core\Queue\Job;
use App\Core\Support\Log;

/**
 * SampleJob demonstrates a small, observable unit of queued work. Dispatch
 * it with queue(new SampleJob()) after configuring a queue worker.
 */
class SampleJob extends Job
{
    public int $tries = 3;
    public int $timeout = 120;

    public function handle(): void
    {
        Log::info('Sample job processed.');
    }
}
