<?php

declare(strict_types=1);

namespace App\Core\Testing;

use PHPUnit\Framework\Assert;

/**
 * Wraps an HTTP response captured during a test so it can be fluently
 * asserted against (status, JSON body, headers, content).
 */
class TestResponse
{
    public function __construct(
        public string $body,
        public int $status,
        public array $headers = []
    ) {
    }

    public function assertStatus(int $expected): self
    {
        Assert::assertSame(
            $expected,
            $this->status,
            "Expected HTTP status {$expected} but received {$this->status}."
        );

        return $this;
    }

    public function assertOk(): self
    {
        return $this->assertStatus(200);
    }

    public function assertNotFound(): self
    {
        return $this->assertStatus(404);
    }

    public function assertForbidden(): self
    {
        return $this->assertStatus(403);
    }

    public function assertJson(array $fragment = []): self
    {
        $data = json_decode($this->body, true);

        Assert::assertIsArray($data, "Response body is not valid JSON: {$this->body}");

        foreach ($fragment as $key => $value) {
            Assert::assertArrayHasKey($key, $data, "JSON fragment missing key '{$key}'.");
            Assert::assertSame($value, $data[$key], "JSON fragment mismatch for key '{$key}'.");
        }

        return $this;
    }

    public function json(): ?array
    {
        $decoded = json_decode($this->body, true);

        return is_array($decoded) ? $decoded : null;
    }

    public function assertSee(string $needle): self
    {
        Assert::assertStringContainsString($needle, $this->body, "Response does not contain '{$needle}'.");

        return $this;
    }

    public function assertHeader(string $name, ?string $expected = null): self
    {
        $value = $this->header($name);

        Assert::assertNotNull($value, "Response does not have header '{$name}'.");

        if ($expected !== null) {
            Assert::assertSame($expected, $value, "Header '{$name}' expected '{$expected}' but got '{$value}'.");
        }

        return $this;
    }

    public function assertRedirect(?string $to = null): self
    {
        $this->assertHeader('Location');

        if ($to !== null) {
            $this->assertHeader('Location', $to);
        }

        return $this;
    }

    public function header(string $name): ?string
    {
        $name = strtolower($name);

        foreach ($this->headers as $key => $value) {
            if (is_int($key)) {
                if (
                    preg_match('/^([^:]+):\s*(.*)$/', (string) $value, $matches)
                    && strtolower(trim($matches[1])) === $name
                ) {
                    return trim($matches[2]);
                }

                continue;
            }

            if (strtolower((string) $key) === $name) {
                return is_array($value) ? implode(', ', $value) : (string) $value;
            }
        }

        return null;
    }
}
