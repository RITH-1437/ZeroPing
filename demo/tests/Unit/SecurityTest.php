<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Security\Encryption;
use App\Core\Security\Hash;
use App\Core\Security\Random;
use App\Core\Support\Config;
use PHPUnit\Framework\TestCase;

class SecurityTest extends TestCase
{
    public function test_hash_make_and_check(): void
    {
        $hash = Hash::make('secret');

        $this->assertNotSame('secret', $hash);
        $this->assertTrue(Hash::check('secret', $hash));
        $this->assertFalse(Hash::check('wrong', $hash));
        $this->assertFalse(Hash::needsRehash($hash));
    }

    public function test_encryption_roundtrip(): void
    {
        Config::set('security.key', openssl_random_pseudo_bytes(32));

        $encrypted = Encryption::encrypt('hello world');

        $this->assertNotSame('hello world', $encrypted);
        $this->assertSame('hello world', Encryption::decrypt($encrypted));
    }

    public function test_random_string_and_uuid(): void
    {
        $string = Random::string(32);

        $this->assertSame(32, strlen($string));
        $this->assertMatchesRegularExpression('/^[0-9a-f]+$/', $string);

        $uuid = Random::uuid();

        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/',
            $uuid
        );
    }
}
