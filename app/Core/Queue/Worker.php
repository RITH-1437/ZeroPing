<?php

namespace App\Core\Queue;

use App\Core\Support\Log;

class Worker
{
    protected QueueManager $manager;

    public function __construct(QueueManager $manager)
    {
        $this->manager = $manager;
    }

    public function run(string $connection, string $queue = null, int $delay = 0, int $sleep = 3, int $maxTries = 1): void
    {
        while (true) {
            $job = $this->getNextJob($connection, $queue);

            if ($job) {
                $this->process($job, $maxTries);
            } else {
                sleep($sleep);
            }

            if ($delay > 0) {
                sleep($delay);
            }
        }
    }

    protected function getNextJob(string $connection, string $queue = null): ?Job
    {
        return $this->manager->connection($connection)->pop($queue);
    }

    protected function process(Job $job, int $maxTries): void
    {
        try {
            $this->raiseBeforeJobEvent($job);
            $job->handle();
            $this->raiseAfterJobEvent($job);
            $this->manager->connection()->delete($job);
        } catch (\Throwable $e) {
            $this->handleJobException($job, $e, $maxTries);
        }
    }

    protected function handleJobException(Job $job, \Throwable $e, int $maxTries): void
    {
        if ($job->attempts() < $maxTries) {
            $this->raiseJobRetryEvent($job);
            $this->manager->connection()->release($job, $job->delay);
        } else {
            $this->raiseJobFailedEvent($job, $e);
            $this->logFailedJob($job, $e);
            $this->manager->connection()->delete($job);
        }
    }

    protected function logFailedJob(Job $job, \Throwable $e): void
    {
        FailedJob::create([
            'connection' => $job->queue,
            'queue' => $job->queue,
            'payload' => $job->toPayload(),
            'exception' => (string) $e,
        ]);
    }

    protected function raiseBeforeJobEvent(Job $job): void
    {
        Log::info("Processing job: " . get_class($job->payload()));
    }

    protected function raiseAfterJobEvent(Job $job): void
    {
        Log::info("Processed job: " . get_class($job->payload()));
    }

    protected function raiseJobFailedEvent(Job $job, \Throwable $e): void
    {
        Log::error("Job failed: " . get_class($job->payload()));
        Log::error($e->getMessage());
    }

    protected function raiseJobRetryEvent(Job $job): void
    {
        Log::info("Retrying job: " . get_class($job->payload()));
    }
}
