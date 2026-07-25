<?php

namespace App\Core\Mail;

use App\Core\Filesystem\Storage;

abstract class Mailable
{
    protected array $to = [];
    protected array $cc = [];
    protected array $bcc = [];
    protected array $from = [];
    protected array $replyTo = [];
    protected string $subject = '';
    protected string $view = '';
    protected array $viewData = [];
    protected string $body = '';
    protected bool $isHtml = false;
    protected array $attachments = [];

    public function to(string $address, ?string $name = null): self
    {
        $this->to[] = new Address($address, $name);

        return $this;
    }

    public function cc(string $address, ?string $name = null): self
    {
        $this->cc[] = new Address($address, $name);

        return $this;
    }

    public function bcc(string $address, ?string $name = null): self
    {
        $this->bcc[] = new Address($address, $name);

        return $this;
    }

    public function from(string $address, ?string $name = null): self
    {
        $this->from[] = new Address($address, $name);

        return $this;
    }

    public function replyTo(string $address, ?string $name = null): self
    {
        $this->replyTo[] = new Address($address, $name);

        return $this;
    }

    public function subject(string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    public function view(string $view, array $data = []): self
    {
        $this->view = $view;
        $this->viewData = $data;

        return $this;
    }

    public function html(string $html): self
    {
        $this->body = $html;
        $this->isHtml = true;

        return $this;
    }

    public function text(string $text): self
    {
        $this->body = $text;

        return $this;
    }

    public function attach(string $file, array $options = []): self
    {
        $this->attachments[] = new Attachment($file, $options);

        return $this;
    }

    public function attachMany(array $files): self
    {
        foreach ($files as $file) {
            $this->attach($file);
        }

        return $this;
    }

    public function attachFromStorage(string $path, ?string $name = null, array $options = []): self
    {
        $contents = Storage::get($path);

        return $this->attachData($contents, $name, $options);
    }

    public function attachData(string $data, string $name, array $options = []): self
    {
        $options['as'] = $name;

        $this->attachments[] = new Attachment($data, $options);

        return $this;
    }

    public function build(): self
    {
        return $this;
    }

    public function getTo(): array
    {
        return $this->to;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getBody(): string
    {
        if ($this->view) {
            return view($this->view, $this->viewData);
        }

        return $this->body;
    }
}
