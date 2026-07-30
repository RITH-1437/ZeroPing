<?php

declare(strict_types=1);

namespace App\Core\Security;

use App\Models\User;
use PDO;

/**
 * Database-backed token repository for password reset tokens.
 *
 * Stores hashed tokens (using password_hash) in a configurable database table,
 * with automatic expiration based on the configured lifetime.
 *
 * Tokens are hashed before storage so that a database breach does not
 * expose raw reset tokens.
 */
class DatabaseTokenRepository
{
    /** @var PDO The database connection. */
    protected PDO $db;

    /** @var string The HMAC key used for generating raw tokens. */
    protected string $hashKey;

    /** @var string The database table name for storing tokens. */
    protected string $table;

    /** @var int Token expiration time in seconds. */
    protected int $expires;

    /**
     * Create a new DatabaseTokenRepository instance.
     *
     * @param PDO    $db      The PDO database connection.
     * @param string $hashKey The HMAC key for token generation.
     * @param string $table   The table name for storing tokens.
     * @param int    $expires Token lifetime in minutes.
     */
    public function __construct(PDO $db, string $hashKey, string $table, int $expires = 60)
    {
        $this->db = $db;
        $this->hashKey = $hashKey;
        $this->table = $table;
        $this->expires = $expires * 60;
    }

    /**
     * Create a new reset token for the given user.
     *
     * Deletes any existing tokens for the user before creating a new one.
     * The raw token is returned for inclusion in the reset email; only
     * the hashed version is stored in the database.
     *
     * @param User $user The user to create a token for.
     * @return string The raw token value.
     */
    public function create(User $user): string
    {
        $this->deleteExisting($user);

        $token = $this->createNewToken();

        $stmt = $this->db->prepare(
            "INSERT INTO {$this->table} (email, token, created_at) VALUES (:email, :token, :created_at)"
        );

        $stmt->execute([
            'email'      => $user->email,
            'token'      => password_hash($token, PASSWORD_DEFAULT),
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return $token;
    }

    /**
     * Determine if a valid token exists for the given user.
     *
     * Validates the token against the stored hash and checks expiration.
     *
     * @param User   $user  The user to check.
     * @param string $token The raw token value to verify.
     * @return bool True if a valid, non-expired token exists.
     */
    public function exists(User $user, string $token): bool
    {
        $stmt = $this->db->prepare(
            "SELECT token, created_at FROM {$this->table} WHERE email = :email LIMIT 1"
        );
        $stmt->execute(['email' => $user->email]);
        $record = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$record) {
            return false;
        }

        if ($this->tokenExpired($record['created_at'])) {
            return false;
        }

        return password_verify($token, $record['token']);
    }

    /**
     * Determine if a token was recently created for the given user.
     *
     * Used to prevent sending multiple reset emails in rapid succession
     * (throttle within 10 seconds by default).
     *
     * @param User $user The user to check.
     * @return bool True if a token was created within the throttle period.
     */
    public function recentlyCreatedToken(User $user): bool
    {
        $stmt = $this->db->prepare(
            "SELECT created_at FROM {$this->table} WHERE email = :email LIMIT 1"
        );
        $stmt->execute(['email' => $user->email]);
        $record = $stmt->fetch(PDO::FETCH_ASSOC);

        return $record !== false && $this->tokenRecentlyCreated($record['created_at']);
    }

    /**
     * Delete all tokens for the given user.
     *
     * @param User $user The user whose tokens should be removed.
     * @return void
     */
    public function delete(User $user): void
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE email = :email");
        $stmt->execute(['email' => $user->email]);
    }

    /**
     * Delete all expired tokens from the repository.
     *
     * Should be called periodically (e.g., via a scheduled task) to
     * keep the token table clean.
     *
     * @return void
     */
    public function deleteExpired(): void
    {
        $expiredAt = date('Y-m-d H:i:s', time() - $this->expires);
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE created_at < :expired_at");
        $stmt->execute(['expired_at' => $expiredAt]);
    }

    /**
     * Delete existing tokens for a user before creating a new one.
     *
     * @param User $user The user whose existing tokens should be removed.
     * @return void
     */
    protected function deleteExisting(User $user): void
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE email = :email");
        $stmt->execute(['email' => $user->email]);
    }

    /**
     * Generate a new cryptographically secure token using HMAC.
     *
     * @return string The raw token string.
     */
    protected function createNewToken(): string
    {
        return hash_hmac('sha256', Random::string(40), $this->hashKey);
    }

    /**
     * Determine if a token has expired based on its creation time.
     *
     * @param string $createdAt The token creation timestamp.
     * @return bool True if the token has expired.
     */
    protected function tokenExpired(string $createdAt): bool
    {
        return (strtotime($createdAt) + $this->expires) < time();
    }

    /**
     * Determine if a token was created within the throttle period (10 seconds).
     *
     * @param string $createdAt The token creation timestamp.
     * @return bool True if the token was created within the last 10 seconds.
     */
    protected function tokenRecentlyCreated(string $createdAt): bool
    {
        if ($this->expires === 0) {
            return true;
        }

        return (strtotime($createdAt) + 10) > time();
    }
}
