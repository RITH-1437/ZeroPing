<?php

namespace App\Core\Scheduling;

class ScheduleManager
{
    protected Schedule $schedule;

    public function __construct()
    {
        $this->schedule = new Schedule();
    }

    public function schedule(): Schedule
    {
        return $this->schedule;
    }

    public function __call(string $method, array $parameters)
    {
        return $this->schedule()->$method(...$parameters);
    }
}
