<?php

namespace App\Models;

use App\Core\Database\Model;

class User extends Model
{
    protected string $table = 'users';

    protected array $fillable = [
        'first_name',
        'last_name',
        'username',
        'email',
        'password',
        'phone',
        'avatar',
        'remember_token',
        'email_verified_at',
    ];

    public function findByEmail(string $email): ?array
    {
        return $this->query()
            ->where('email', $email)
            ->first();
    }

    public function findByUsername(string $username): ?array
    {
        return $this->query()
            ->where('username', $username)
            ->first();
    }
}
