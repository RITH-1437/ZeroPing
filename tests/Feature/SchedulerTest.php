<?php

namespace Tests\Feature;

use App\Core\Scheduling\Schedule;
use App\Core\Testing\TestCase;

class SchedulerTest extends TestCase
{
    public function test_can_schedule_command()
    {
        $schedule = new Schedule();
        $schedule->command('cache:clear')->daily();
        $this->assertCount(1, $schedule->events());
    }
}
