<?php

namespace App\Core\Mail;

class Message
{
    public array $to = [];
    public string $subject = '';

    public function to(string $address, ?string $name = null): self
    {
        $this->to[] = new Address($address, $name);
        return $this;
    }

    public function subject(string $subject): self
    {
        $this->subject = $subject;
        return $this;
    }
}
