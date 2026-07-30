<?php

declare(strict_types=1);

namespace App\Core\Events;

/**
 * Base class for all application events.
 *
 * Events represent something that has happened in the application.
 * They carry data and can be dispatched to registered listeners
 * via the {@see EventDispatcher}.
 *
 * Extend this class to create domain-specific events:
 *
 * @example
 * ```php
 * class UserRegistered extends Event
 * {
 *     public function __construct(
 *         public readonly User $user,
 *         public readonly \DateTimeImmutable $registeredAt,
 *     ) {}
 * }
 * ```
 */
abstract class Event
{
    /**
     * Indicates whether propagation of the event to further listeners should be stopped.
     *
     * @var bool
     */
    private bool $propagationStopped = false;

    /**
     * Stop the propagation of this event to further listeners.
     *
     * Once called, no subsequent listeners will be invoked for this event instance.
     *
     * @return void
     */
    public function stopPropagation(): void
    {
        $this->propagationStopped = true;
    }

    /**
     * Check whether propagation has been stopped.
     *
     * @return bool True if propagation is stopped.
     */
    public function isPropagationStopped(): bool
    {
        return $this->propagationStopped;
    }
}
