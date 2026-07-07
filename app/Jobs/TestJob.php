<?php

namespace App\Jobs;

use App\Core\Queue\Job;
use App\Core\Support\Log;

class TestJob extends Job
{
    public function handle(): void
    {
        Log::info('Test job handled successfully!');
    }
}
