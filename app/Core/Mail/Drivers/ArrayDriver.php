<?php

namespace App\Core\Mail\Drivers;

use App\Core\Mail\Mailable;

class ArrayDriver implements MailDriver
{
    protected array $sentMessages = [];

    public function send(Mailable $mailable): bool
    {
        $this->sentMessages[] = $mailable;

        return true;
    }

    public function sentMessages(): array
    {
        return $this->sentMessages;
    }
}
