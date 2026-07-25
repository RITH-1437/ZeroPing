<?php
declare(strict_types=1);

namespace App\Core\Mail\Drivers;

use App\Core\Mail\Mailable;

interface MailDriver
{
    public function send(Mailable $mailable): bool;
}
