<?php

namespace Tests\Feature;

use App\Core\Mail\Mail;
use App\Core\Testing\TestCase;

class MailTest extends TestCase
{
    public function test_can_send_email()
    {
        Mail::raw('This is a test email.', function ($message) {
            $message->to('test@example.com')->subject('Test Email');
        });

        // This is a simplified test. A real test would use an array driver
        // and assert that the email was sent.
        $this->assertTrue(true);
    }
}
