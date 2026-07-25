<?php
declare(strict_types=1);

namespace App\Core\Mail;

class MailMessage extends Mailable
{
    public function text(string $text): self
    {
        $this->body = $text;

        return $this;
    }

    public function html(string $html): self
    {
        $this->body = $html;
        $this->isHtml = true;

        return $this;
    }
}
