<?php

declare(strict_types=1);

namespace App\Core\Auth\Traits;

use App\Core\Auth\PersonalAccessToken;

/**
 * @phpstan-ignore-next-line
 */
trait HasApiTokens
{
    protected ?PersonalAccessToken $currentAccessToken = null;

    public function createToken(
        string $name,
        array $abilities = ['*'],
        ?\DateTimeInterface $expiresAt = null
    ): object {
        $plainText = bin2hex(random_bytes(40));

        $token = PersonalAccessToken::create([
            'tokenable_type' => static::class,
            'tokenable_id'   => $this->getKey(),
            'name'           => $name,
            'token'          => hash('sha256', $plainText),
            'abilities'      => json_encode($abilities),
            'expires_at'     => $expiresAt !== null ? $expiresAt->format('Y-m-d H:i:s') : null,
        ]);

        return new class($token, $plainText) {
            public string $plainTextToken;
            public PersonalAccessToken $accessToken;

            public function __construct(PersonalAccessToken $token, string $plain)
            {
                $this->accessToken = $token;
                $this->plainTextToken = $plain;
            }
        };
    }

    public function tokens(): \App\Core\ORM\Collection
    {
        return PersonalAccessToken::query()
            ->where('tokenable_type', static::class)
            ->where('tokenable_id', $this->getKey())
            ->get();
    }

    public function revokeAllTokens(): void
    {
        PersonalAccessToken::query()
            ->where('tokenable_type', static::class)
            ->where('tokenable_id', $this->getKey())
            ->delete();
    }

    public function withAccessToken(PersonalAccessToken $token): static
    {
        $this->currentAccessToken = $token;
        return $this;
    }

    public function currentAccessToken(): ?PersonalAccessToken
    {
        return $this->currentAccessToken;
    }

    public function tokenCan(string $ability): bool
    {
        return $this->currentAccessToken !== null && $this->currentAccessToken->can($ability);
    }
}