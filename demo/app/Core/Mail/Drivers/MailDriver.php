<?php

namespace App\Core\Mail\Drivers;

use App\Core\Mail\Mailable;

interface MailDriver
{
    public function send(Mailable $mailable): bool;
}
