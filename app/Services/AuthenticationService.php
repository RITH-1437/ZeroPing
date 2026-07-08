<?php

namespace App\Services;

use App\Core\Auth\AuthManager;
use App\Core\Auth\PasswordHasher;
use App\Repositories\UserRepository;
use App\Core\Validation\Validator;

class AuthenticationService
{
    public function __construct(
        private UserRepository $users
    ) {
    }

    public function register(array $data): bool
    {
        $validator = Validator::make($data, [
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'username' => 'required|string|max:100',
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:6|max:255',
            'phone' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return false;
        }

        $email = (string) ($data['email'] ?? '');
        $username = (string) ($data['username'] ?? '');

        if ($this->users->findByEmail($email)) {
            return false;
        }

        if ($this->users->findByUsername($username)) {
            return false;
        }

        return $this->users->create([
            'first_name' => (string) ($data['first_name'] ?? ''),
            'last_name'  => (string) ($data['last_name'] ?? ''),
            'username'   => $username,
            'email'      => $email,
            'password'   => PasswordHasher::make((string) ($data['password'] ?? '')),
            'role'       => 'customer',
            'phone'      => $data['phone'] ?? null,
        ]);
    }

    public function login(string $email, string $password): bool
    {
        $user = $this->users->findByEmail($email);

        if (!$user) {
            return false;
        }

        if (!PasswordHasher::check($password, $user['password'])) {
            return false;
        }

        AuthManager::login($user);

        return true;
    }
}