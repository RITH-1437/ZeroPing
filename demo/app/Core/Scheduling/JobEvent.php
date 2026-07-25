<?php

namespace App\Core\Scheduling;

use App\Core\Queue\Dispatcher;
use App\Core\Queue\Job;

class JobEvent extends Event
{
    protected Job $job;

    public function __construct(Job $job)
    {
        $this->job = $job;
    }

    public function run(): void
    {
        Dispatcher::dispatch($this->job);
    }
}
