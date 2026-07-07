<?php

namespace App\Core\Testing;

use App\Core\Testing\Database\DatabaseAssertions;
use App\Core\Testing\Exceptions\TestingException;

trait Assertion
{
    use DatabaseAssertions;

    public function assertTrue(bool $condition, string $message = ''): void
    {
        if (!$condition) {
            throw new TestingException($message ?: 'Failed asserting that condition is true.');
        }
    }

    public function assertFalse(bool $condition, string $message = ''): void
    {
        if ($condition) {
            throw new TestingException($message ?: 'Failed asserting that condition is false.');
        }
    }

    public function assertNull($value, string $message = ''): void
    {
        if (!is_null($value)) {
            throw new TestingException($message ?: 'Failed asserting that value is null.');
        }
    }

    public function assertNotNull($value, string $message = ''): void
    {
        if (is_null($value)) {
            throw new TestingException($message ?: 'Failed asserting that value is not null.');
        }
    }

    public function assertEquals($expected, $actual, string $message = ''): void
    {
        if ($expected != $actual) {
            throw new TestingException($message ?: 'Failed asserting that two values are equal.');
        }
    }

    public function assertNotEquals($expected, $actual, string $message = ''): void
    {
        if ($expected == $actual) {
            throw new TestingException($message ?: 'Failed asserting that two values are not equal.');
        }
    }

    public function assertCount(int $expected, $haystack, string $message = ''): void
    {
        if (count($haystack) !== $expected) {
            throw new TestingException($message ?: 'Failed asserting that count matches expected.');
        }
    }

    public function assertEmpty($value, string $message = ''): void
    {
        if (!empty($value)) {
            throw new TestingException($message ?: 'Failed asserting that value is empty.');
        }
    }

    public function assertNotEmpty($value, string $message = ''): void
    {
        if (empty($value)) {
            throw new TestingException($message ?: 'Failed asserting that value is not empty.');
        }
    }

    public function assertInstanceOf(string $expected, $actual, string $message = ''): void
    {
        if (!$actual instanceof $expected) {
            throw new TestingException($message ?: 'Failed asserting that object is an instance of expected class.');
        }
    }

    public function assertThrows(\Closure $closure, string $expectedException = \Throwable::class): void
    {
        try {
            $closure();
        } catch (\Throwable $e) {
            $this->assertInstanceOf($expectedException, $e);
            return;
        }

        throw new TestingException("Expected exception '{$expectedException}' was not thrown.");
    }

    public function assertContains($needle, $haystack, string $message = ''): void
    {
        if (!in_array($needle, $haystack)) {
            throw new TestingException($message ?: 'Failed asserting that haystack contains needle.');
        }
    }

    public function assertStringContainsString(string $needle, string $haystack, string $message = ''): void
    {
        if (strpos($haystack, $needle) === false) {
            throw new TestingException($message ?: 'Failed asserting that string contains substring.');
        }
    }

    public function assertStringNotContainsString(string $needle, string $haystack, string $message = ''): void
    {
        if (strpos($haystack, $needle) !== false) {
            throw new TestingException($message ?: 'Failed asserting that string does not contain substring.');
        }
    }

    public function assertModelExists(\App\Core\Database\Model $model): void
    {
        $this->assertDatabaseHas($model->getTable(), ['id' => $model->id]);
    }
}
