<?php

class AuthService
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function register(array $data): bool
    {
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

            'password' => Hash::make($data['password']),

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

        if (!Hash::check($password, $user['password'])) {
            return false;
        }

        Auth::login($user);

        return true;
    }

}