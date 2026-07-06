<?php

namespace App\Core\Events;

interface Listener
{
    public function handle(Event $event): void;
}