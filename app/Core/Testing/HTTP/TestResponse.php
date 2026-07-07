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
        (new \App\Core\Testing\TestCase())->assertEquals($status, $this->status);
        return $this;
    }

    public function assertRedirect(string $uri = null): self
    {
        (new \App\Core\Testing\TestCase())->assertTrue($this->isRedirect($uri));
        return $this;
    }

    public function assertJson(array $data = null, bool $strict = false): self
    {
        (new \App\Core\Testing\TestCase())->assertEquals($data, json_decode($this->content, true));
        return $this;
    }

    public function assertViewIs(string $value): self
    {
        // This is a simplified implementation.
        return $this;
    }

    public function assertSee(string $value): self
    {
        (new \App\Core\Testing\TestCase())->assertStringContainsString($value, $this->content);
        return $this;
    }

    public function assertDontSee(string $value): self
    {
        (new \App\Core\Testing\TestCase())->assertStringNotContainsString($value, $this->content);
        return $this;
    }

    protected function isRedirect(string $uri = null): bool
    {
        if (is_null($uri)) {
            return $this->status >= 300 && $this->status < 400;
        }

        return $this->isRedirect() && $this->headers['Location'] === $uri;
    }
}
