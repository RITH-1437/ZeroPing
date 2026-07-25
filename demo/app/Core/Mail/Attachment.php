<?php

namespace App\Core\Mail;

class Attachment
{
    public string $file;
    public array $options;

    public function __construct(string $file, array $options = [])
    {
        $this->file = $file;
        $this->options = $options;
    }
}
