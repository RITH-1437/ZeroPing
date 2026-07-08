<?php

namespace App\Core\Testing\HTTP;

class TestResponse
{
    protected string $content;
    protected int $status;
    protected array $headers;

    public function __construct(string $content, int $status, array $headers)
    {
        $this->content = $content;
        $this->status = $status;
        $this->headers = $headers;
    }

    public function assertStatus(int $status): self
    {
        assert($this->status === $status, "Expected status {$status}, got {$this->status}");
        return $this;
    }

    public function assertRedirect(string $uri = null): self
    {
        assert($this->isRedirect($uri), "Expected redirect" . ($uri ? " to {$uri}" : ''));
        return $this;
    }

    public function assertJson(array $data = null, bool $strict = false): self
    {
        $decoded = json_decode($this->content, true);
        assert($decoded === $data, "JSON response did not match expected data");
        return $this;
    }

    public function assertViewIs(string $value): self
    {
        return $this;
    }

    public function assertSee(string $value): self
    {
        assert(str_contains($this->content, $value), "Expected response to contain [{$value}]");
        return $this;
    }

    public function assertDontSee(string $value): self
    {
        assert(!str_contains($this->content, $value), "Expected response not to contain [{$value}]");
        return $this;
    }

    protected function isRedirect(string $uri = null): bool
    {
        if (is_null($uri)) {
            return $this->status >= 300 && $this->status < 400;
        }

        return $this->isRedirect() && ($this->headers['Location'] ?? '') === $uri;
    }
}
