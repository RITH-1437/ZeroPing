<?php

namespace Zeroping\Queue\Contracts;

/**
 * Resolves named queue connections and lets hosts register new ones.
 */
interface QueueManager
{
    /**
     * Get (or default) queue connection.
     */
    public function connection(?string $name = null): Queue;

    /**
     * Register a connection configuration (driver => implementation mapping).
     *
     * @param array<string, mixed> $config
     */
    public function addConnection(string $name, array $config): void;
}
