<?php

namespace Tests\Feature;

use App\Core\Testing\TestCase;
use App\Models\User;

class UserTest extends TestCase
{
    public function test_can_create_user()
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => 'password',
        ]);

        $this->assertModelExists($user);
    }
}
