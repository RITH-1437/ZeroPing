<?php

namespace App\Core\Console\Commands;

use App\Core\Console\Command;
use App\Core\Mail\Mail;

class MailTestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected string $signature = 'mail:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected string $description = 'Send a test email';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->info('Sending test email...');

        Mail::raw('This is a test email.', function ($message) {
            $message->to('test@example.com')->subject('Test Email');
        });

        $this->info('Test email sent successfully!');
    }
}
