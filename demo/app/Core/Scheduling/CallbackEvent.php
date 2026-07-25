<?php

namespace App\Core\Scheduling;

class CallbackEvent extends Event
{
    protected $callback;

    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    public function run(): void
    {
        call_user_func($this->callback);
    }
}
