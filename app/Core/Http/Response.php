<?php

declare(strict_types=1);

namespace App\Core\Http;

/**
 * An object-oriented HTTP response.
 *
 * Unlike App\Http\Response (which is a static helper that calls header/echo),
 * this object can be built, inspected and sent without terminating the
 * process, which keeps it usable from tests and from the HTTP kernel.
 *
 * @since 1.0.0
 * @author Rin Nairith
 */
class Response
{
    protected mixed $content;

    protected int $status;

    /** @var array<string, string> */
    protected array $headers;

    /** @var array{status: int, headers: array<string, string>}|null */
    protected static ?array $lastSent = null;

    /**
     * Create a new Response instance.
     *
     * @param mixed                 $content Response body content
     * @param int                   $status  HTTP status code
     * @param array<string, string> $headers Response headers
     */
    public function __construct(mixed $content = '', int $status = 200, array $headers = [])
    {
        $this->content = $content;
        $this->status  = $status;
        $this->headers = $headers;
    }

    /**
     * Create a JSON response.
     *
     * @param mixed $data   Data to encode as JSON
     * @param int   $status HTTP status code
     * @return self
     *
     * @throws \JsonException If JSON encoding fails
     */
    public static function json(mixed $data, int $status = 200): self
    {
        return new self(
            json_encode($data, JSON_THROW_ON_ERROR),
            $status,
            ['Content-Type' => 'application/json']
        );
    }

    /**
     * Set the HTTP status code (mutable).
     *
     * @param int $status HTTP status code
     * @return self Fluent return for chaining
     */
    public function status(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Set the HTTP status code (fluent alias).
     *
     * @param int $status HTTP status code
     * @return self Fluent return for chaining
     */
    public function withStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Merge multiple headers into the response.
     *
     * @param array<string, string> $headers Headers to merge
     * @return self Fluent return for chaining
     */
    public function withHeaders(array $headers): self
    {
        $this->headers = array_merge($this->headers, $headers);

        return $this;
    }

    /**
     * Set a single response header.
     *
     * @param string $key   Header name
     * @param string $value Header value
     * @return self Fluent return for chaining
     */
    public function header(string $key, string $value): self
    {
        $this->headers[$key] = $value;

        return $this;
    }

    /**
     * Set a single response header (fluent alias).
     *
     * @param string $key   Header name
     * @param string $value Header value
     * @return self Fluent return for chaining
     */
    public function withHeader(string $key, string $value): self
    {
        $this->headers[$key] = $value;

        return $this;
    }

    /**
     * Set the response body content.
     *
     * @param mixed $content The response body
     * @return self Fluent return for chaining
     */
    public function withContent(mixed $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get the response body content.
     *
     * @return mixed The response body
     */
    public function content(): mixed
    {
        return $this->content;
    }

    /**
     * Get the HTTP status code.
     *
     * @return int The HTTP status code
     */
    public function getStatusCode(): int
    {
        return $this->status;
    }

    /**
     * Get all response headers.
     *
     * @return array<string, string> The response headers
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Get a specific header value.
     *
     * @param string      $key     The header name
     * @param string|null $default Default value if header is not set
     * @return string|null The header value or default
     */
    public function getHeader(string $key, ?string $default = null): ?string
    {
        return $this->headers[$key] ?? $default;
    }

    /**
     * Determine if the response has a given header.
     *
     * @param string $key The header name to check
     * @return bool True if the header exists
     */
    public function hasHeader(string $key): bool
    {
        return isset($this->headers[$key]);
    }

    /**
     * Determine if the response is successful (2xx status).
     *
     * @return bool True if status is between 200 and 299
     */
    public function isSuccessful(): bool
    {
        return $this->status >= 200 && $this->status < 300;
    }

    /**
     * Determine if the response is a redirect (3xx status).
     *
     * @return bool True if status is between 300 and 399
     */
    public function isRedirect(): bool
    {
        return $this->status >= 300 && $this->status < 400;
    }

    /**
     * Send the response to the client.
     *
     * @return void
     */
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

    /**
     * Get the last sent response metadata (for testing).
     *
     * @return array{status: int, headers: array<string, string>}|null
     */
    public static function lastSent(): ?array
    {
        return self::$lastSent;
    }

    /**
     * Reset the last-sent metadata (for testing).
     *
     * @return void
     */
    public static function resetLastSent(): void
    {
        self::$lastSent = null;
    }
}
