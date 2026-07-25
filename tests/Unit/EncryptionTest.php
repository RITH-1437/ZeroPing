<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Security\Encryption;
use App\Core\Security\Hash;

class EncryptionTest extends \Tests\TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        \App\Core\Support\Config::set('security.key', '0123456789abcdef0123456789abcdef');
    }

    protected function tearDown(): void
    {
        \App\Core\Support\Config::set('security.key', '');
        parent::tearDown();
    }

    public function testEncryptReturnsString(): void
    {
        $encrypted = Encryption::encrypt('test-value');

        $this->assertIsString($encrypted);
        $this->assertNotEmpty($encrypted);
    }

    public function testDecryptReturnsOriginalValue(): void
    {
        $original = 'sensitive-data';
        $encrypted = Encryption::encrypt($original);
        $decrypted = Encryption::decrypt($encrypted);

        $this->assertSame($original, $decrypted);
    }

    public function testEncryptReturnsDifferentResultsEachTime(): void
    {
        $value = 'hello';

        $a = Encryption::encrypt($value);
        $b = Encryption::encrypt($value);

        $this->assertNotSame($a, $b);
    }

    public function testDecryptDifferentValues(): void
    {
        $data = [
            'password123',
            'hello@world.com',
            '{"json":"data"}',
            'a',
        ];

        foreach ($data as $original) {
            $encrypted = Encryption::encrypt($original);
            $decrypted = Encryption::decrypt($encrypted);
            $this->assertSame($original, $decrypted);
        }
    }

    public function testEncryptedDataHasValidGcmFormat(): void
    {
        $encrypted = Encryption::encrypt('test');

        $decoded = base64_decode($encrypted, true);

        $this->assertNotFalse($decoded);
        $this->assertGreaterThanOrEqual(29, strlen($decoded));
    }

    public function testMakeAndCheckPasswordHash(): void
    {
        $hash = Hash::make('my-password');

        $this->assertTrue(Hash::check('my-password', $hash));
        $this->assertFalse(Hash::check('wrong-password', $hash));
    }
}
