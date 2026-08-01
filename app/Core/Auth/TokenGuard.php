<?php

declare(strict_types=1);

namespace App\Core\Auth;

class TokenGuard
{
    protected mixed $user = null;
    protected bool $resolved = false;

    public function user(): mixed
    {
        if ($this->resolved) {
            return $this->user;
        }

        $this->resolved = true;
        $token = $this->getBearerToken();

        if ($token === null) {
            return null;
        }

        $pat = PersonalAccessToken::findToken($token);

        if ($pat === null || $pat->isExpired()) {
            return null;
        }

        $pat->update(['last_used_at' => date('Y-m-d H:i:s')]);

        $tokenableClass = $pat->tokenable_type;
        if (!class_exists($tokenableClass)) {
            return null;
        }

        $tokenable = $tokenableClass::find($pat->tokenable_id);
        if ($tokenable !== null && is_object($tokenable) && method_exists($tokenable, 'withAccessToken')) {
            $tokenable->withAccessToken($pat);
        }

        $this->user = $tokenable;
        return $this->user;
    }

    public function check(): bool
    {
        return $this->user() !== null;
    }

    public function guest(): bool
    {
        return !$this->check();
    }

    public function id(): int|string|null
    {
        $user = $this->user();
        if ($user === null) {
            return null;
        }
        return method_exists($user, 'getKey') && is_object($user) ? $user->getKey() : null;
    }

    protected function getBearerToken(): ?string
    {
        $header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        if (str_starts_with($header, 'Bearer ')) {
            return substr($header, 7);
        }
        return null;
    }
}