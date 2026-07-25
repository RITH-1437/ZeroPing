<?php

namespace App\Mail;

use App\Core\Mail\Mailable;

/**
 * SampleMail — illustrative mailable.
 *
 * Mailables build an email message. Return the view to render in build().
 * Send with `Mail::send(new SampleMail())`.
 */
class SampleMail extends Mailable
{
    public function build(): self
    {
        return $this->view('emails.sample');
    }
}
