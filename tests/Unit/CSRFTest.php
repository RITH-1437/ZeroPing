<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Security\CSRF;

class CSRFTest extends \Tests\TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION = [];
    }

    public function testGenerateReturnsTokenString(): void
    {
        $token = CSRF::generate();

        $this->assertIsString($token);
        $this->assertNotEmpty($token);
    }

    public function testGenerateReturns64CharacterHexString(): void
    {
        $token = CSRF::generate();

        $this->assertMatchesRegularExpression('/^[0-9a-f]{64}$/', $token);
    }

    public function testCheckReturnsTrueForGeneratedToken(): void
    {
        $token = CSRF::generate();

        $this->assertTrue(CSRF::check($token));
    }

    public function testCheckReturnsFalseForInvalidToken(): void
    {
        CSRF::generate();

        $this->assertFalse(CSRF::check('invalid-token'));
    }

    public function testCheckReturnsFalseForEmptyString(): void
    {
        $this->assertFalse(CSRF::check(''));
    }

    public function testMultipleTokensAreValid(): void
    {
        $token1 = CSRF::generate();
        $token2 = CSRF::generate();
        $token3 = CSRF::generate();

        $this->assertTrue(CSRF::check($token1));
        $this->assertTrue(CSRF::check($token2));
        $this->assertTrue(CSRF::check($token3));
    }

    public function testOldTokensAreEvictedAfterTen(): void
    {
        $tokens = [];
        for ($i = 0; $i < 12; $i++) {
            $tokens[] = CSRF::generate();
        }

        $this->assertFalse(CSRF::check($tokens[0]));
        $this->assertFalse(CSRF::check($tokens[1]));
        $this->assertTrue(CSRF::check($tokens[2]));
        $this->assertTrue(CSRF::check($tokens[11]));
    }

    public function testGenerateReturnsUniqueTokens(): void
    {
        $token1 = CSRF::generate();
        $token2 = CSRF::generate();

        $this->assertNotSame($token1, $token2);
    }
}
