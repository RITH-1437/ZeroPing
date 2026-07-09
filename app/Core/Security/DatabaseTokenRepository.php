<?php

namespace App\Core\Security;

use App\Models\User;

class DatabaseTokenRepository
{
    protected \PDO $db;
    protected string $hashKey;
    protected string $table;
    protected int $expires;

    public function __construct(\PDO $db, string $hashKey, string $table, int $expires = 60)
    {
        $this->db = $db;
        $this->hashKey = $hashKey;
        $this->table = $table;
        $this->expires = $expires * 60;
    }

    public function create(User $user): string
    {
        $this->deleteExisting($user);

        $token = $this->createNewToken();

        $stmt = $this->db->prepare(
            "INSERT INTO {$this->table} (email, token, created_at) VALUES (?, ?, ?)"
        );
        $stmt->execute([
            $user->email,
            password_hash($token, PASSWORD_DEFAULT),
            date('Y-m-d H:i:s'),
        ]);

        return $token;
    }

    public function exists(User $user, string $token): bool
    {
        $stmt = $this->db->prepare(
            "SELECT token, created_at FROM {$this->table} WHERE email = ? LIMIT 1"
        );
        $stmt->execute([$user->email]);
        $record = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$record) {
            return false;
        }

        if ($this->tokenExpired($record['created_at'])) {
            return false;
        }

        return password_verify($token, $record['token']);
    }

    public function recentlyCreatedToken(User $user): bool
    {
        $stmt = $this->db->prepare(
            "SELECT created_at FROM {$this->table} WHERE email = ? LIMIT 1"
        );
        $stmt->execute([$user->email]);
        $record = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $record && $this->tokenRecentlyCreated($record['created_at']);
    }

    public function delete(User $user): void
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE email = ?");
        $stmt->execute([$user->email]);
    }

    public function deleteExpired(): void
    {
        $expiredAt = date('Y-m-d H:i:s', time() - $this->expires);
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE created_at < ?");
        $stmt->execute([$expiredAt]);
    }

    protected function deleteExisting(User $user): void
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE email = ?");
        $stmt->execute([$user->email]);
    }

    protected function createNewToken(): string
    {
        return hash_hmac('sha256', Random::string(40), $this->hashKey);
    }

    protected function tokenExpired(string $createdAt): bool
    {
        return strtotime($createdAt) + $this->expires < time();
    }

    protected function tokenRecentlyCreated(string $createdAt): bool
    {
        if ($this->expires === 0) {
            return true;
        }
        return strtotime($createdAt) + 10 > time();
    }
}
