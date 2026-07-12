<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Auth\SessionGuard;

class SessionGuardTest extends \Tests\TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION = [];
    }

    public function testSetAndGet(): void
    {
        SessionGuard::set('key', 'value');
        $this->assertSame('value', SessionGuard::get('key'));
    }

    public function testGetReturnsDefault(): void
    {
        $this->assertSame('default', SessionGuard::get('missing', 'default'));
    }

    public function testHas(): void
    {
        SessionGuard::set('exists', true);
        $this->assertTrue(SessionGuard::has('exists'));
        $this->assertFalse(SessionGuard::has('missing'));
    }

    public function testRemove(): void
    {
        SessionGuard::set('temp', 'data');
        SessionGuard::remove('temp');
        $this->assertFalse(SessionGuard::has('temp'));
    }

    public function testDestroyClearsSession(): void
    {
        SessionGuard::set('a', 1);
        SessionGuard::set('b', 2);
        SessionGuard::destroy();
        $this->assertFalse(SessionGuard::has('a'));
        $this->assertFalse(SessionGuard::has('b'));
    }

    public function testDestroyKeepsSessionUsable(): void
    {
        SessionGuard::set('x', 1);
        SessionGuard::destroy();
        SessionGuard::set('y', 2);
        $this->assertSame(2, SessionGuard::get('y'));
    }
}
