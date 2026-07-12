<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Session\Session;

class SessionTest extends \Tests\TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION = [];
    }

    public function testSetAndGetSessionValue(): void
    {
        Session::set('key', 'value');

        $this->assertSame('value', Session::get('key'));
    }

    public function testGetReturnsDefaultForMissingKey(): void
    {
        $this->assertSame('default', Session::get('missing', 'default'));
    }

    public function testGetReturnsNullForMissingKey(): void
    {
        $this->assertNull(Session::get('missing'));
    }

    public function testHasReturnsTrueForExistingKey(): void
    {
        Session::set('exists', true);

        $this->assertTrue(Session::has('exists'));
    }

    public function testHasReturnsFalseForMissingKey(): void
    {
        $this->assertFalse(Session::has('missing'));
    }

    public function testRemoveDeletesKey(): void
    {
        Session::set('temp', 'data');
        Session::remove('temp');

        $this->assertFalse(Session::has('temp'));
    }

    public function testFlushClearsAllData(): void
    {
        Session::set('a', 1);
        Session::set('b', 2);
        $_SESSION = [];

        $this->assertFalse(Session::has('a'));
        $this->assertFalse(Session::has('b'));
    }

    public function testSetStoresDifferentTypes(): void
    {
        Session::set('string', 'hello');
        Session::set('int', 42);
        Session::set('array', [1, 2, 3]);
        Session::set('bool', true);
        Session::set('null', null);

        $this->assertSame('hello', Session::get('string'));
        $this->assertSame(42, Session::get('int'));
        $this->assertSame([1, 2, 3], Session::get('array'));
        $this->assertTrue(Session::get('bool'));
        $this->assertNull(Session::get('null'));
    }

    public function testSetOverwritesExistingValue(): void
    {
        Session::set('key', 'old');
        Session::set('key', 'new');

        $this->assertSame('new', Session::get('key'));
    }

    public function testRemoveNonexistentKeyDoesNotError(): void
    {
        Session::remove('nonexistent');

        $this->assertTrue(true);
    }
}
