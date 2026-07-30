<?php

declare(strict_types=1);

namespace App\Core\Events;

/**
 * Dispatches events to registered listeners with priority support.
 *
 * Listeners are invoked in descending priority order (higher numbers run first).
 * Listeners with the same priority are invoked in the order they were registered.
 * Event propagation can be halted by calling `$event->stopPropagation()`.
 *
 * @example
 * ```php
 * $dispatcher = new EventDispatcher();
 *
 * // Register with priority (higher = earlier execution)
 * $dispatcher->listen(UserRegistered::class, SendWelcomeEmail::class, priority: 10);
 * $dispatcher->listen(UserRegistered::class, NotifyAdmin::class, priority: 5);
 *
 * // Dispatch — SendWelcomeEmail runs before NotifyAdmin
 * $dispatcher->dispatch(new UserRegistered($user));
 * ```
 */
class EventDispatcher
{
    /**
     * Registered listeners grouped by event class and priority.
     *
     * Structure: [eventClass => [priority => [listenerClass, ...]]]
     *
     * @var array<string, array<int, array<int, class-string<Listener>>>>
     */
    protected array $listeners = [];

    /**
     * Sorted listener cache to avoid re-sorting on every dispatch.
     *
     * Structure: [eventClass => [listenerClass, ...]]
     *
     * @var array<string, array<int, class-string<Listener>>>
     */
    protected array $sorted = [];

    /**
     * Register a listener for an event.
     *
     * @param class-string<Event> $event The fully-qualified event class name.
     * @param class-string<Listener> $listener The fully-qualified listener class name.
     * @param int $priority The listener priority (higher = runs first). Default: 0.
     * @return void
     */
    public function listen(string $event, string $listener, int $priority = 0): void
    {
        $this->listeners[$event][$priority][] = $listener;

        // Invalidate the sorted cache for this event
        unset($this->sorted[$event]);
    }

    /**
     * Dispatch an event to all registered listeners.
     *
     * Listeners are invoked in descending priority order. If a listener
     * calls `$event->stopPropagation()`, no further listeners will be invoked.
     *
     * @param Event $event The event instance to dispatch.
     * @return Event The same event instance (may have been modified by listeners).
     */
    public function dispatch(Event $event): Event
    {
        $eventClass = get_class($event);
        $listeners = $this->getListenersForEvent($eventClass);

        foreach ($listeners as $listenerClass) {
            if ($event->isPropagationStopped()) {
                break;
            }

            /** @var Listener $listener */
            $listener = new $listenerClass();
            $listener->handle($event);
        }

        return $event;
    }

    /**
     * Check if an event has any registered listeners.
     *
     * @param class-string<Event> $event The event class name.
     * @return bool True if at least one listener is registered.
     */
    public function hasListeners(string $event): bool
    {
        return !empty($this->listeners[$event]);
    }

    /**
     * Get all listeners for an event, sorted by priority (descending).
     *
     * Results are cached until a new listener is registered for the event.
     *
     * @param string $eventClass The event class name.
     * @return array<int, class-string<Listener>> The sorted listener class names.
     */
    public function getListenersForEvent(string $eventClass): array
    {
        if (isset($this->sorted[$eventClass])) {
            return $this->sorted[$eventClass];
        }

        if (!isset($this->listeners[$eventClass])) {
            return [];
        }

        $listeners = $this->listeners[$eventClass];

        // Sort by priority descending (higher priority runs first)
        krsort($listeners);

        // Flatten the grouped listeners into a single array
        $sorted = [];
        foreach ($listeners as $group) {
            foreach ($group as $listener) {
                $sorted[] = $listener;
            }
        }

        return $this->sorted[$eventClass] = $sorted;
    }

    /**
     * Remove all listeners for a specific event, or all listeners entirely.
     *
     * @param class-string<Event>|null $event The event class name, or null to clear all.
     * @return void
     */
    public function forget(?string $event = null): void
    {
        if ($event === null) {
            $this->listeners = [];
            $this->sorted = [];
        } else {
            unset($this->listeners[$event], $this->sorted[$event]);
        }
    }
}
