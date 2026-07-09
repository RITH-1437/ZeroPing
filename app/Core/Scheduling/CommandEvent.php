<?php

namespace App\Core\Scheduling;

class CommandEvent extends Event
{
    public function run(): void
    {
        passthru(escapeshellcmd($this->command));
    }
}
