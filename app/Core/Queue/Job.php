<?php

namespace App\Core\Queue;

abstract class Job
{
    public int $tries = 1;
    public int $timeout = 60;
    public int $delay = 0;
    public ?string $queue = null;

    protected array $job;

    public function __construct(array $job = [])
    {
        $this->job = $job;
    }

    abstract public function handle(): void;

    public function failed(\Throwable $exception): void
    {
        //
    }

    public function id(): ?int
    {
        return $this->job['id'] ?? null;
    }

    public function attempts(): int
    {
        return $this->job['attempts'] ?? 0;
    }

    public function payload(): array
    {
        return $this->job['payload'] ?? [];
    }

    public function toPayload(): string
    {
        return json_encode([
            '_class' => static::class,
            'tries' => $this->tries,
            'timeout' => $this->timeout,
            'delay' => $this->delay,
            'queue' => $this->queue,
            'job' => $this->job,
        ]);
    }

    public static function fromPayload(string $payload): ?static
    {
        $data = json_decode($payload, true);
        if (!$data || !isset($data['_class'])) {
            return null;
        }
        $class = $data['_class'];
        if (!class_exists($class) || !is_subclass_of($class, self::class)) {
            return null;
        }
        $job = new $class($data['job'] ?? []);
        $job->tries = $data['tries'] ?? 1;
        $job->timeout = $data['timeout'] ?? 60;
        $job->delay = $data['delay'] ?? 0;
        $job->queue = $data['queue'] ?? null;
        return $job;
    }
}
