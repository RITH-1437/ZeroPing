<?php

declare(strict_types=1);

namespace App\Core\Scheduling;

interface Mutex
{
    public function create(Event $event): bool;

    public function exists(Event $event): bool;

    public function forget(Event $event): void;
}
