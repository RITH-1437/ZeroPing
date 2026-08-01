<?php

declare(strict_types=1);

namespace App\Core\Http;

/**
 * Represents the response from an outgoing HTTP request made via Client.
 *
 * @since 2.1.0
 */
class ClientResponse
{
    /** @param array<string, string> $headers */
    public function __construct(
        protected readonly int $status,
        protected readonly array $headers,
        protected readonly string $body,
    ) {
    }

    public function status(): int
    {
        return $this->status;
    }

    public function body(): string
    {
        return $this->body;
    }

    /** @return array<string, string> */
    public function headers(): array
    {
        return $this->headers;
    }

    public function header(string $name): ?string
    {
        return $this->headers[strtolower($name)] ?? null;
    }

    /** @return array<mixed>|null */
    public function json(): ?array
    {
        $decoded = json_decode($this->body, true);
        return is_array($decoded) ? $decoded : null;
    }

    public function object(): ?object
    {
        $decoded = json_decode($this->body);
        return is_object($decoded) ? $decoded : null;
    }

    public function ok(): bool
    {
        return $this->status >= 200 && $this->status < 300;
    }
    public function successful(): bool
    {
        return $this->ok();
    }
    public function failed(): bool
    {
        return !$this->ok();
    }
    public function redirect(): bool
    {
        return $this->status >= 300 && $this->status < 400;
    }
    public function clientError(): bool
    {
        return $this->status >= 400 && $this->status < 500;
    }
    public function serverError(): bool
    {
        return $this->status >= 500;
    }
    public function notFound(): bool
    {
        return $this->status === 404;
    }
    public function unauthorized(): bool
    {
        return $this->status === 401;
    }
    public function forbidden(): bool
    {
        return $this->status === 403;
    }

    /**
     * Throw a RuntimeException if the response was not successful.
     *
     * @throws \RuntimeException
     */
    public function throw(): static
    {
        if ($this->failed()) {
            throw new \RuntimeException(
                sprintf('HTTP request failed with status %d: %s', $this->status, $this->body)
            );
        }
        return $this;
    }

    public function __toString(): string
    {
        return $this->body;
    }
}
