<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Security\Hash;

class HashTest extends \Tests\TestCase
{
    public function testMakeReturnsBcryptHash(): void
    {
        $hash = Hash::make('password123');

        $this->assertIsString($hash);
        $this->assertStringStartsWith('$2y$', $hash);
    }

    public function testMakeReturnsDifferentHashesForSameInput(): void
    {
        $hash1 = Hash::make('password123');
        $hash2 = Hash::make('password123');

        $this->assertNotSame($hash1, $hash2);
    }

    public function testCheckReturnsTrueForCorrectPassword(): void
    {
        $hash = Hash::make('secret');

        $this->assertTrue(Hash::check('secret', $hash));
    }

    public function testCheckReturnsFalseForIncorrectPassword(): void
    {
        $hash = Hash::make('secret');

        $this->assertFalse(Hash::check('wrong', $hash));
    }

    public function testCheckReturnsFalseForEmptyPassword(): void
    {
        $hash = Hash::make('secret');

        $this->assertFalse(Hash::check('', $hash));
    }

    public function testNeedsRehashReturnsFalseForFreshHash(): void
    {
        $hash = Hash::make('password');

        $this->assertFalse(Hash::needsRehash($hash));
    }

    public function testMakeWorksWithEmptyString(): void
    {
        $hash = Hash::make('');

        $this->assertIsString($hash);
        $this->assertTrue(Hash::check('', $hash));
    }

    public function testMakeWorksWithLongString(): void
    {
        $long = str_repeat('a', 1000);
        $hash = Hash::make($long);

        $this->assertTrue(Hash::check($long, $hash));
    }
}
