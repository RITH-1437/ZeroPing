<?php

namespace Zeroping\Queue;

use Zeroping\Queue\Contracts\Job;
use Zeroping\Queue\Contracts\Queue;
use Zeroping\Queue\Contracts\QueueManager as QueueManagerContract;

/**
 * Default QueueManager implementation.
 *
 * This is a SCAFFOLD: connection resolution is stubbed with a sync queue so the
 * architecture compiles and runs. Concrete drivers (database, redis) are bound
 * by the host or added in later iterations (see ARCHITECTURE.md §8.2).
 */
class QueueManager implements QueueManagerContract
{
    /** @var array<string, array<string, mixed>> */
    protected array $connections = [];

    /** @var array<string, Queue> */
    protected array $resolved = [];

    public function __construct(array $config = [])
    {
        foreach ($config['connections'] ?? [] as $name => $conn) {
            $this->addConnection($name, $conn);
        }
    }

    public function addConnection(string $name, array $config): void
    {
        $this->connections[$name] = $config;
    }

    public function connection(?string $name = null): Queue
    {
        $name ??= 'default';

        if (isset($this->resolved[$name])) {
            return $this->resolved[$name];
        }

        if (!isset($this->connections[$name])) {
            throw new \InvalidArgumentException("Queue connection [{$name}] is not defined.");
        }

        return $this->resolved[$name] = $this->resolve($this->connections[$name]);
    }

    /**
     * Build a Queue for the given connection config.
     *
     * Hook point for drivers: switch on $config['driver'] and return the
     * matching Queue implementation. Defaults to an in-memory sync queue.
     */
    protected function resolve(array $config): Queue
    {
        return new SyncQueue(); // placeholder driver
    }
}
