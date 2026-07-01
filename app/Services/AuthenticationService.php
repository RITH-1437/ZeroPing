<?php

namespace App\Services;

use App\Auth\AuthManager;
use App\Auth\PasswordHasher;
use App\Models\User;
use App\Support\Validator;

class AuthenticationService
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
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
            print_r($validator->errors());
            return false;
        }

        if ($this->userModel->findByEmail($data['email'])) {

            return false;
        }

        if ($this->userModel->findByUsername($data['username'])) {

            return false;
        }

        return $this->userModel->create([

            'first_name' => $data['first_name'],

            'last_name' => $data['last_name'],

            'username' => $data['username'],

            'email' => $data['email'],

            'password' => PasswordHasher::make($data['password']),

            'role' => 'customer',

            'phone' => $data['phone'] ?? null
        ]);
    }

    public function login(string $email, string $password): bool
    {
        $user = $this->userModel->findByEmail($email);

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