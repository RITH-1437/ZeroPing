<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Auth\AuthManager;
use App\Core\Auth\SessionGuard;
use App\Core\Security\CSRF;

class AuthManagerTest extends \Tests\TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION = [];
    }

    public function testLoginSetsUserInSession(): void
    {
        $user = ['id' => 1, 'name' => 'John', 'email' => 'john@example.com'];

        AuthManager::login($user);

        $this->assertSame($user, SessionGuard::get('user'));
    }

    public function testCheckReturnsTrueWhenLoggedIn(): void
    {
        AuthManager::login(['id' => 1]);

        $this->assertTrue(AuthManager::check());
    }

    public function testCheckReturnsFalseWhenNotLoggedIn(): void
    {
        $this->assertFalse(AuthManager::check());
    }

    public function testUserReturnsUserData(): void
    {
        $user = ['id' => 5, 'name' => 'Jane'];
        AuthManager::login($user);

        $this->assertSame($user, AuthManager::user());
    }

    public function testUserReturnsNullWhenNotLoggedIn(): void
    {
        $this->assertNull(AuthManager::user());
    }

    public function testIdReturnsUserId(): void
    {
        AuthManager::login(['id' => 42]);

        $this->assertSame(42, AuthManager::id());
    }

    public function testIdReturnsNullWhenNotLoggedIn(): void
    {
        $this->assertNull(AuthManager::id());
    }

    public function testLogoutClearsSession(): void
    {
        AuthManager::login(['id' => 1, 'name' => 'John']);
        $this->assertTrue(AuthManager::check());

        AuthManager::logout();

        $this->assertFalse(AuthManager::check());
        $this->assertNull(AuthManager::user());
    }

    public function testLoginOverwritesPreviousUser(): void
    {
        AuthManager::login(['id' => 1, 'name' => 'First']);
        AuthManager::login(['id' => 2, 'name' => 'Second']);

        $this->assertSame(2, AuthManager::id());
        $this->assertSame('Second', AuthManager::user()['name']);
    }
}
