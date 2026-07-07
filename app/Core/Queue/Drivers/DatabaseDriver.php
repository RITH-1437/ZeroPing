<?php

namespace App\Core\Queue\Drivers;

use App\Core\Database\Database;
use App\Core\Queue\Job;

class DatabaseDriver implements QueueDriver
{
    protected \PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function push(Job $job, string $queue = null): void
    {
        $this->db->prepare(
            "INSERT INTO jobs (queue, payload, attempts, reserved_at, available_at, created_at)
             VALUES (?, ?, ?, ?, ?, ?)"
        )->execute([
            $queue,
            serialize($job),
            0,
            null,
            time(),
            time(),
        ]);
    }

    public function later(int $delay, Job $job, string $queue = null): void
    {
        $this->db->prepare(
            "INSERT INTO jobs (queue, payload, attempts, reserved_at, available_at, created_at)
             VALUES (?, ?, ?, ?, ?, ?)"
        )->execute([
            $queue,
            serialize($job),
            0,
            null,
            time() + $delay,
            time(),
        ]);
    }

    public function pop(string $queue = null): ?Job
    {
        $this->db->beginTransaction();

        $stmt = $this->db->prepare(
            "SELECT * FROM jobs
             WHERE queue = ? AND reserved_at IS NULL AND available_at <= ?
             ORDER BY id ASC
             LIMIT 1
             FOR UPDATE"
        );

        $stmt->execute([$queue, time()]);

        $job = $stmt->fetch();

        if ($job) {
            $this->db->prepare(
                "UPDATE jobs SET reserved_at = ? WHERE id = ?"
            )->execute([time(), $job['id']]);

            $this->db->commit();

            $job['payload'] = unserialize($job['payload']);

            return new Job($job);
        }

        $this->db->commit();

        return null;
    }

    public function delete(Job $job): void
    {
        $this->db->prepare(
            "DELETE FROM jobs WHERE id = ?"
        )->execute([$job->id()]);
    }

    public function release(Job $job, int $delay = 0): void
    {
        $this->db->prepare(
            "UPDATE jobs SET reserved_at = NULL, available_at = ? WHERE id = ?"
        )->execute([time() + $delay, $job->id()]);
    }
}
