<?php

declare(strict_types=1);

namespace App\Core\Events;

/**
 * Contract for event listeners.
 *
 * All event listeners must implement this interface and define
 * a `handle()` method that processes the event.
 *
 * @example
 * ```php
 * class SendWelcomeEmail implements Listener
 * {
 *     public function handle(Event $event): void
 *     {
 *         // Send welcome email to $event->user...
 *     }
 * }
 * ```
 */
interface Listener
{
    /**
     * Handle the given event.
     *
     * If the listener needs to prevent subsequent listeners from being called,
     * it should call `$event->stopPropagation()`.
     *
     * @param Event $event The event instance to handle.
     * @return void
     */
    public function handle(Event $event): void;
}
