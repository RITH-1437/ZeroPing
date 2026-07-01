<?php

namespace App\Services;

use App\Auth\AuthManager;
use App\Auth\PasswordHasher;
use App\Repositories\UserRepository;
use App\Support\Validator;

class AuthenticationService
{
    public function __construct(
        private UserRepository $users
    ) {
    }

    public function register(array $data): bool
    {
        $validator = new Validator();

        $validator
            ->required('first_name', $data['first_name'])
            ->required('last_name', $data['last_name'])
            ->required('username', $data['username'])
            ->required('email', $data['email'])
            ->required('password', $data['password'])
            ->email('email', $data['email'])
            ->min('password', $data['password'], 6);

        if (!$validator->passes()) {
            return false;
        }

        if ($this->users->findByEmail($data['email'])) {
            return false;
        }

        if ($this->users->findByUsername($data['username'])) {
            return false;
        }

        return $this->users->create([
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
            'username'   => $data['username'],
            'email'      => $data['email'],
            'password'   => PasswordHasher::make($data['password']),
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