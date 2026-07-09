<?php

namespace App\Mail;

use App\Core\Mail\Mailable;

class TestMail extends Mailable
{
    public function build(): self
    {
        return $this->view('emails.test_mail');
    }
}
