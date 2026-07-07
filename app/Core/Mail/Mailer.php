<?php

namespace App\Core\Mail;

use App\Core\Mail\Drivers\MailDriver;

class Mailer
{
    protected MailDriver $driver;

    public function __construct(MailDriver $driver)
    {
        $this->driver = $driver;
    }

    public function to(string $address, string $name = null): self
    {
        $this->to[] = new Address($address, $name);

        return $this;
    }

    public function send(Mailable $mailable): bool
    {
        return $this->driver->send($mailable);
    }
}
