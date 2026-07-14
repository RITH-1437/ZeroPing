<?php

declare(strict_types=1);

namespace App\Core\Http;

/**
 * An object-oriented HTTP response.
 *
 * Unlike App\Http\Response (which is a static helper that calls exit),
 * this object can be built, inspected and sent without terminating the
 * process, which keeps it usable from tests and from the HTTP kernel.
 */
class Response
{
    protected mixed $content;

    protected int $status;

    protected array $headers;

    protected static ?array $lastSent = null;

    public function __construct(mixed $content = '', int $status = 200, array $headers = [])
    {
        $this->content = $content;
        $this->status  = $status;
        $this->headers = $headers;
    }

    public static function json(mixed $data, int $status = 200): self
    {
        return new self(
            json_encode($data, JSON_THROW_ON_ERROR),
            $status,
            ['Content-Type' => 'application/json']
        );
    }

    public function status(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function withHeaders(array $headers): self
    {
        $this->headers = array_merge($this->headers, $headers);

        return $this;
    }

    public function header(string $key, string $value): self
    {
        $this->headers[$key] = $value;

        return $this;
    }

    public function content(): mixed
    {
        return $this->content;
    }

    public function getStatusCode(): int
    {
        return $this->status;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function send(): void
    {
        self::$lastSent = [
            'status'  => $this->status,
            'headers' => $this->headers,
        ];

        if (!headers_sent()) {
            http_response_code($this->status);

            foreach ($this->headers as $key => $value) {
                header("{$key}: {$value}");
            }
        }

        echo $this->content;
    }

    public static function lastSent(): ?array
    {
        return self::$lastSent;
    }

    public static function resetLastSent(): void
    {
        self::$lastSent = null;
    }
}
