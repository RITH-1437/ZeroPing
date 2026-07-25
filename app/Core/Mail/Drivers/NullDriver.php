<?php
declare(strict_types=1);

namespace App\Core\Mail\Drivers;

use App\Core\Mail\Mailable;

class NullDriver implements MailDriver
{
    public function send(Mailable $mailable): bool
    {
        return true;
    }
}
