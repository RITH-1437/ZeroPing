<?php

declare(strict_types=1);

namespace App\Core\Testing;

use App\Core\ORM\Model;
use App\Core\Testing\Database\DatabaseAssertions;
use App\Core\Testing\Exceptions\TestingException;

/**
 * Framework-specific assertions that complement (never duplicate) the
 * assertions provided by PHPUnit\Framework\TestCase. The base TestCase
 * already exposes assertTrue/assertArrayHasKey/assertContains/etc., so
 * this trait only adds behaviour that PHPUnit does not provide.
 */
trait Assertion
{
    use DatabaseAssertions;

    /**
     * Assert that the given closure throws the expected exception type.
     */
    public function assertThrowsException(\Closure $closure, string $expectedException = \Throwable::class): void
    {
        try {
            $closure();
        } catch (\Throwable $e) {
            $this->assertInstanceOf($expectedException, $e);

            return;
        }

        throw new TestingException("Expected exception '{$expectedException}' was not thrown.");
    }

    /**
     * Assert that a persisted model row exists in the database.
     */
    public function assertModelExists(Model $model): void
    {
        $this->assertDatabaseHas($model->getTable(), ['id' => $model->id]);
    }
}
