<?php

declare(strict_types=1);

namespace App\Core\Auth;

use App\Core\Database\Model;

/**
 * @property string|null $abilities
 * @property string|null $expires_at
 * @property string $tokenable_type
 * @property string $tokenable_id
 */
class PersonalAccessToken extends Model
{
    protected string $table = 'personal_access_tokens';

    /** @var array<int, string> */
    protected array $fillable = [
        'tokenable_type',
        'tokenable_id',
        'name',
        'token',
        'abilities',
        'last_used_at',
        'expires_at',
    ];

    public static function findToken(string $rawToken): ?static
    {
        $hash = hash('sha256', $rawToken);
        return static::query()->where('token', $hash)->first();
    }

    public function can(string $ability): bool
    {
        $abilities = $this->abilities !== null
            ? json_decode($this->abilities, true)
            : ['*'];

        if (!is_array($abilities)) {
            return false;
        }

        return in_array('*', $abilities, true) || in_array($ability, $abilities, true);
    }

    public function isExpired(): bool
    {
        if ($this->expires_at === null) {
            return false;
        }
        return strtotime($this->expires_at) < time();
    }
}