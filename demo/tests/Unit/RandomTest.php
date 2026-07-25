<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Security\Random;

class RandomTest extends \Tests\TestCase
{
    public function testStringReturnsCorrectLength(): void
    {
        $str = Random::string(32);

        $this->assertSame(32, strlen($str));
    }

    public function testStringReturnsHexString(): void
    {
        $str = Random::string(16);

        $this->assertMatchesRegularExpression('/^[0-9a-f]+$/', $str);
    }

    public function testStringReturnsUniqueValues(): void
    {
        $a = Random::string(32);
        $b = Random::string(32);

        $this->assertNotSame($a, $b);
    }

    public function testStringDefaultLengthIs32(): void
    {
        $str = Random::string();

        $this->assertSame(32, strlen($str));
    }

    public function testUuidReturnsValidFormat(): void
    {
        $uuid = Random::uuid();

        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/',
            $uuid
        );
    }

    public function testUuidReturnsUniqueValues(): void
    {
        $a = Random::uuid();
        $b = Random::uuid();

        $this->assertNotSame($a, $b);
    }

    public function testUuidVersion4Indicator(): void
    {
        $uuid = Random::uuid();

        $this->assertSame('4', $uuid[14]);
    }

    public function testUuidVariantBits(): void
    {
        $uuid = Random::uuid();

        $this->assertContains($uuid[19], ['8', '9', 'a', 'b']);
    }

    public function testTokenReturnsCorrectLength(): void
    {
        $token = Random::token(40);

        $this->assertSame(40, strlen($token));
    }

    public function testTokenDefaultLengthIs60(): void
    {
        $token = Random::token();

        $this->assertSame(60, strlen($token));
    }

    public function testTokenReturnsHexString(): void
    {
        $token = Random::token(20);

        $this->assertMatchesRegularExpression('/^[0-9a-f]+$/', $token);
    }
}
