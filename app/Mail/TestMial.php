<?php

namespace App\Mail;

use App\Core\Mail\Mailable;

class TestMial extends Mailable
{
    public function build(): self
    {
        return $this->view('emails.{{ view }}');
    }
}
