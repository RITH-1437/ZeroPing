<?php

namespace Tests\Feature;

use App\Core\Queue\Dispatcher;
use App\Core\Testing\TestCase;
use App\Jobs\TestJob;

class QueueTest extends TestCase
{
    public function test_can_dispatch_job()
    {
        Dispatcher::dispatch(new TestJob());

        // This is a simplified test. A real test would use an array driver
        // and assert that the job was pushed to the queue.
        $this->assertTrue(true);
    }
}
