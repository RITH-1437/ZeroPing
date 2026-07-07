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
}
