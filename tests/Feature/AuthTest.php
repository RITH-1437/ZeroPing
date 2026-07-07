<?php

namespace Tests\Feature;

use App\Core\Testing\TestCase;
use App\Models\User;

class AuthTest extends TestCase
{
    public function test_user_can_login()
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => 'password',
        ]);

        $response = $this->post('/login', [
            'email' => 'john.doe@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
    }
}
