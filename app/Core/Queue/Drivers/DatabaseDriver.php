<?php

declare(strict_types=1);

namespace App\Core\Queue\Drivers;

use App\Core\Database\Database;
use App\Core\Queue\Job;

/**
 * Database-backed queue driver.
 *
 * Stores queue jobs in a database table, supporting delayed execution,
 * atomic job reservation (via SELECT ... FOR UPDATE), and reliable
 * delivery through transaction-based popping.
 *
 * Required table schema (jobs):
 * - id: bigint unsigned auto_increment primary key
 * - queue: varchar(255)
 * - payload: longtext
 * - attempts: tinyint unsigned default 0
 * - reserved_at: int unsigned nullable
 * - available_at: int unsigned
 * - created_at: int unsigned
 */
class DatabaseDriver implements QueueDriver
{
    /**
     * The PDO database connection.
     *
     * @var \PDO
     */
    protected \PDO $db;

    /**
     * The default queue name.
     *
     * @var string
     */
    protected string $defaultQueue;

    /**
     * The jobs table name.
     *
     * @var string
     */
    protected string $table;

    /**
     * Create a new database queue driver instance.
     *
     * @param array<string, mixed> $config Configuration options:
     *   - 'table': The jobs table name (default: 'jobs').
     *   - 'queue': The default queue name (default: 'default').
     */
    public function __construct(array $config = [])
    {
        $this->db = Database::connect();
        $this->table = $config['table'] ?? 'jobs';
        $this->defaultQueue = $config['queue'] ?? 'default';
    }

    /**
     * {@inheritdoc}
     */
    public function push(Job $job, ?string $queue = null): void
    {
        $queue = $queue ?? $this->defaultQueue;

        $this->db->prepare(
            "INSERT INTO {$this->table} (queue, payload, attempts, reserved_at, available_at, created_at)
             VALUES (?, ?, ?, ?, ?, ?)"
        )->execute([
            $queue,
            $job->toPayload(),
            0,
            null,
            time(),
            time(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function later(int $delay, Job $job, ?string $queue = null): void
    {
        $queue = $queue ?? $this->defaultQueue;

        $this->db->prepare(
            "INSERT INTO {$this->table} (queue, payload, attempts, reserved_at, available_at, created_at)
             VALUES (?, ?, ?, ?, ?, ?)"
        )->execute([
            $queue,
            $job->toPayload(),
            0,
            null,
            time() + $delay,
            time(),
        ]);
    }

    /**
     * {@inheritdoc}
     *
     * Uses a transaction with SELECT ... FOR UPDATE to atomically
     * reserve the next available job, preventing race conditions
     * between multiple workers.
     */
    public function pop(?string $queue = null): ?Job
    {
        $queue = $queue ?? $this->defaultQueue;

        $this->db->beginTransaction();

        try {
            $stmt = $this->db->prepare(
                "SELECT * FROM {$this->table}
                 WHERE queue = ? AND reserved_at IS NULL AND available_at <= ?
                 ORDER BY id ASC
                 LIMIT 1
                 FOR UPDATE"
            );

            $stmt->execute([$queue, time()]);
            $record = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($record === false) {
                $this->db->commit();
                return null;
            }

            // Mark the job as reserved
            $this->db->prepare(
                "UPDATE {$this->table} SET reserved_at = ?, attempts = attempts + 1 WHERE id = ?"
            )->execute([time(), $record['id']]);

            $this->db->commit();

            // Restore the Job instance from the payload
            $restored = Job::fromPayload($record['payload']);

            if ($restored === null) {
                // Payload is corrupted or class no longer exists — delete the record
                $this->db->prepare("DELETE FROM {$this->table} WHERE id = ?")->execute([$record['id']]);
                return null;
            }

            return $restored;
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function delete(Job $job): void
    {
        $id = $job->id();

        if ($id !== null) {
            $this->db->prepare(
                "DELETE FROM {$this->table} WHERE id = ?"
            )->execute([$id]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function release(Job $job, int $delay = 0): void
    {
        $id = $job->id();

        if ($id !== null) {
            $this->db->prepare(
                "UPDATE {$this->table} SET reserved_at = NULL, available_at = ? WHERE id = ?"
            )->execute([time() + $delay, $id]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function size(?string $queue = null): int
    {
        $queue = $queue ?? $this->defaultQueue;

        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM {$this->table} WHERE queue = ? AND reserved_at IS NULL"
        );
        $stmt->execute([$queue]);

        return (int) $stmt->fetchColumn();
    }
}
