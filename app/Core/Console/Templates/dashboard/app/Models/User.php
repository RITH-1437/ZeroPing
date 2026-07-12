<?php

namespace App\Models;

use App\Core\Model;

class User extends Model
{
    protected string $table = 'users';

    protected array $fillable = [
        'name', 'email', 'password',
    ];

    protected array $hidden = [
        'password',
    ];

    public function setPasswordAttribute(string $value): void
    {
        $this->attributes['password'] = password_hash($value, PASSWORD_BCRYPT);
    }
}
