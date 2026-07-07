<?php

namespace Tests\Feature;

use App\Core\Security\Hash;
use App\Core\Testing\TestCase;

class SecurityTest extends TestCase
{
    public function test_can_hash_password()
    {
        $hash = Hash::make('password');
        $this->assertTrue(Hash::check('password', $hash));
    }
}
