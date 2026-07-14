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

    public function getDriver(): MailDriver
    {
        return $this->driver;
    }

    public function to(string $address, ?string $name = null): self
    {
        $this->to[] = new Address($address, $name);

        return $this;
    }

    public function send(Mailable $mailable): bool
    {
        return $this->driver->send($mailable);
    }

    public function raw(string $text, \Closure $callback): bool
    {
        $message = new Message();
        $callback($message);
        // Raw send — log the message if driver supports it, otherwise no-op
        if (method_exists($this->driver, 'sendRaw')) {
            return $this->driver->sendRaw($message, $text);
        }
        return true;
    }
}
