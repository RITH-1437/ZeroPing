<?php

namespace App\Core\Mail;

class MailRepository
{
    protected Mailer $mailer;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function send(Mailable $mailable): bool
    {
        return $this->mailer->send($mailable);
    }

    public function queue(Mailable $mailable): bool
    {
        // This is a simplified implementation. A real implementation would
        // use a queueing system.
        return $this->send($mailable);
    }

    public function later(int $delay, Mailable $mailable): bool
    {
        // This is a simplified implementation. A real implementation would
        // use a queueing system.
        sleep($delay);

        return $this->send($mailable);
    }

    public function raw(string $text, callable $callback): bool
    {
        $message = new MailMessage();
        $message->text($text);

        $callback($message);

        return $this->mailer->send($message);
    }

    public function html(string $html, callable $callback): bool
    {
        $message = new MailMessage();
        $message->html($html);

        $callback($message);

        return $this->mailer->send($message);
    }

    public function text(string $text, callable $callback): bool
    {
        return $this->raw($text, $callback);
    }
}
