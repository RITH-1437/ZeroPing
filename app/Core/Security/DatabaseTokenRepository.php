<?php

namespace App\Core\Security;

use App\Core\Database\Database;
use Carbon\Carbon;

class DatabaseTokenRepository
{
    protected $db;
    protected $hashKey;
    protected $table;
    protected $expires;

    public function __construct(\PDO $db, $hashKey, $table, $expires = 60)
    {
        $this->db = $db;
        $this->hashKey = $hashKey;
        $this->table = $table;
        $this->expires = $expires * 60;
    }

    public function create(User $user)
    {
        $email = $user->email;

        $this->deleteExisting($user);

        $token = $this->createNewToken();

        $this->getTable()->insert($this->getPayload($email, $token));

        return $token;
    }

    public function exists(User $user, $token)
    {
        $record = (array) $this->getTable()->where('email', $user->email)->first();

        return $record &&
               ! $this->tokenExpired($record['created_at']) &&
                 password_verify($token, $record['token']);
    }

    public function recentlyCreatedToken(User $user)
    {
        $record = (array) $this->getTable()->where('email', $user->email)->first();

        return $record && $this->tokenRecentlyCreated($record['created_at']);
    }

    public function delete(User $user)
    {
        $this->getTable()->where('email', $user->email)->delete();
    }

    public function deleteExpired()
    {
        $expiredAt = Carbon::now()->subSeconds($this->expires);

        $this->getTable()->where('created_at', '<', $expiredAt)->delete();
    }

    protected function deleteExisting(User $user)
    {
        return $this->getTable()->where('email', $user->email)->delete();
    }

    protected function getPayload($email, $token)
    {
        return ['email' => $email, 'token' => password_hash($token, PASSWORD_DEFAULT), 'created_at' => new Carbon];
    }

    protected function getTable()
    {
        return $this->db->table($this->table);
    }

    protected function createNewToken()
    {
        return hash_hmac('sha256', Random::string(40), $this->hashKey);
    }

    protected function tokenExpired($createdAt)
    {
        return Carbon::parse($createdAt)->addSeconds($this->expires)->isPast();
    }

    protected function tokenRecentlyCreated($createdAt)
    {
        if ($this->expires === 0) {
            return true;
        }

        return Carbon::parse($createdAt)->addSeconds(10)->isFuture();
    }
}
