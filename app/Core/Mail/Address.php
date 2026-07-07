<?php

namespace App\Core\Mail;

class Address
{
    public string $address;
    public ?string $name;

    public function __construct(string $address, ?string $name = null)
    {
        $this->address = $address;
        $this->name = $name;
    }
}
