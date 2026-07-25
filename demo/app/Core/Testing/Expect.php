<?php

declare(strict_types=1);

namespace App\Core\Testing;

use PHPUnit\Framework\Assert;

/**
 * Fluent value expectations (expect($value)->toBe(...)) backed by
 * PHPUnit's assertion library so they integrate with the test runner.
 */
class Expect
{
    protected $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function toBe($expected): void
    {
        Assert::assertEquals($expected, $this->value);
    }

    public function toBeNull(): void
    {
        Assert::assertNull($this->value);
    }

    public function toBeTrue(): void
    {
        Assert::assertTrue($this->value);
    }

    public function toBeFalse(): void
    {
        Assert::assertFalse($this->value);
    }

    public function toContain($needle): void
    {
        Assert::assertContains($needle, $this->value);
    }

    public function toHaveCount(int $count): void
    {
        Assert::assertCount($count, $this->value);
    }

    public function toBeInstanceOf(string $class): void
    {
        Assert::assertInstanceOf($class, $this->value);
    }
}
