<?php

namespace App\Events;

use App\Core\Events\Event;

class UserRegistered extends Event
{
    public function __construct(
        public array $user
    ) {
    }
}
