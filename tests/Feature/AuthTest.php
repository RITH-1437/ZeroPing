<?php

namespace Tests\Feature;

use App\Core\Testing\TestCase;
use App\Models\User;

class AuthTest extends TestCase
{
    public function test_user_can_login()
    {
        $user = User::create([
            'first_name' => 'John',
            'last_name'  => 'Doe',
            'username'   => 'johndoe_auth',
            'email'      => 'john.auth@example.com',
            'password'   => password_hash('password', PASSWORD_DEFAULT),
        ]);

        $response = $this->post('/login', [
            'email' => 'john.doe@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
    }
}
