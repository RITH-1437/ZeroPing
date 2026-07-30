<?php

declare(strict_types=1);

namespace App\Core\Queue;

/**
 * Base class for all queue jobs.
 *
 * Provides the foundation for creating queueable jobs with configurable
 * retry attempts, timeouts, and delays. Implements the JobInterface contract
 * and handles serialization/deserialization of job payloads.
 *
 * Extend this class and implement the `handle()` method to define your job logic.
 *
 * @example
 * ```php
 * class SendEmailJob extends Job
 * {
 *     public function handle(): void
 *     {
 *         // Send the email...
 *     }
 * }
 * ```
 *
 * @since 1.0.0
 * @author Rin Nairith
 * @link https://zero-ping.duckdns.org/docs/queues
 */
abstract class Job implements JobInterface
{
    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public int $tries = 1;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public int $timeout = 60;

    /**
     * The number of seconds to wait before making the job available.
     *
     * @var int
     */
    public int $delay = 0;

    /**
     * The queue connection/name this job should be dispatched to.
     *
     * @var string|null
     */
    public ?string $queue = null;

    /**
     * The raw job data from the queue driver.
     *
     * @var array<string, mixed>
     */
    protected array $job;

    /**
     * Whitelist of allowed job classes for deserialization.
     *
     * When null, any subclass of Job is allowed.
     * When set, only listed classes can be deserialized.
     *
     * @var array<int, class-string<Job>>|null
     */
    private static ?array $allowedClasses = null;

    /**
     * Create a new job instance.
     *
     * @param array<string, mixed> $job The raw job data (typically from queue storage).
     */
    public function __construct(array $job = [])
    {
        $this->job = $job;
    }

    /**
     * {@inheritdoc}
     */
    abstract public function handle(): void;

    /**
     * {@inheritdoc}
     */
    public function failed(\Throwable $exception): void
    {
        // Override in subclasses to handle failure notifications, cleanup, etc.
    }

    /**
     * {@inheritdoc}
     */
    public function id(): ?int
    {
        return isset($this->job['id']) ? (int) $this->job['id'] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function attempts(): int
    {
        return (int) ($this->job['attempts'] ?? 0);
    }

    /**
     * {@inheritdoc}
     */
    public function getMaxTries(): int
    {
        return $this->tries;
    }

    /**
     * {@inheritdoc}
     */
    public function getTimeout(): int
    {
        return $this->timeout;
    }

    /**
     * {@inheritdoc}
     */
    public function getDelay(): int
    {
        return $this->delay;
    }

    /**
     * {@inheritdoc}
     */
    public function getQueue(): ?string
    {
        return $this->queue;
    }

    /**
     * Get the decoded payload data for this job.
     *
     * @return array<string, mixed>
     */
    public function payload(): array
    {
        return $this->job['payload'] ?? [];
    }

    /**
     * {@inheritdoc}
     */
    public function toPayload(): string
    {
        $encoded = json_encode([
            '_class' => static::class,
            'tries' => $this->tries,
            'timeout' => $this->timeout,
            'delay' => $this->delay,
            'queue' => $this->queue,
            'job' => $this->job,
        ], JSON_THROW_ON_ERROR);

        return $encoded;
    }

    /**
     * Add a class to the deserialization whitelist.
     *
     * When at least one class is added, only whitelisted classes
     * may be instantiated from a payload. This prevents arbitrary
     * class instantiation from untrusted payloads.
     *
     * @param class-string<Job> $class The fully-qualified class name to allow.
     * @return void
     */
    public static function allowClass(string $class): void
    {
        self::$allowedClasses ??= [];
        self::$allowedClasses[] = $class;
    }

    /**
     * Get the list of allowed classes for deserialization.
     *
     * @return array<int, class-string<Job>>|null Null if no whitelist is set.
     */
    public static function getAllowedClasses(): ?array
    {
        return self::$allowedClasses;
    }

    /**
     * Clear the deserialization whitelist.
     *
     * Primarily useful in testing to reset state between tests.
     *
     * @return void
     */
    public static function clearAllowedClasses(): void
    {
        self::$allowedClasses = null;
    }

    /**
     * Restore a Job instance from a JSON payload string.
     *
     * Validates the payload structure, checks the class against the
     * whitelist (if set), and instantiates the job with its original
     * configuration.
     *
     * @param string $payload The JSON-encoded payload string.
     * @return static|null The restored job instance, or null if restoration fails.
     */
    public static function fromPayload(string $payload): ?static
    {
        $data = json_decode($payload, true);

        if (!is_array($data) || !isset($data['_class'])) {
            return null;
        }

        $class = $data['_class'];

        if (!is_string($class) || !class_exists($class) || !is_subclass_of($class, self::class)) {
            return null;
        }

        if (self::$allowedClasses !== null && !in_array($class, self::$allowedClasses, true)) {
            return null;
        }

        /** @var static $job */
        $job = new $class($data['job'] ?? []);
        $job->tries = (int) ($data['tries'] ?? 1);
        $job->timeout = (int) ($data['timeout'] ?? 60);
        $job->delay = (int) ($data['delay'] ?? 0);
        $job->queue = $data['queue'] ?? null;

        return $job;
    }
}
