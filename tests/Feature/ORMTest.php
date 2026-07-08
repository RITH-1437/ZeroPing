<?php

namespace Tests\Feature;

use App\Core\Testing\TestCase;
use App\Models\User;

class ORMTest extends TestCase
{
    public function test_can_create_user()
    {
        $user = User::create([
            'first_name' => 'John',
            'last_name'  => 'Doe',
            'username'   => 'johndoe_orm',
            'email'      => 'john.orm@example.com',
            'password'   => password_hash('password', PASSWORD_DEFAULT),
        ]);

        $this->assertModelExists($user);
    }
}
