<?php

namespace App\Core\Testing;

class Expect
{
    protected $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function toBe($expected): void
    {
        (new TestCase())->assertEquals($expected, $this->value);
    }

    public function toBeNull(): void
    {
        (new TestCase())->assertNull($this->value);
    }

    public function toBeTrue(): void
    {
        (new TestCase())->assertTrue($this->value);
    }

    public function toBeFalse(): void
    {
        (new TestCase())->assertFalse($this->value);
    }

    public function toContain($needle): void
    {
        (new TestCase())->assertContains($needle, $this->value);
    }

    public function toHaveCount(int $count): void
    {
        (new TestCase())->assertCount($count, $this->value);
    }

    public function toBeInstanceOf(string $class): void
    {
        (new TestCase())->assertInstanceOf($class, $this->value);
    }
}
