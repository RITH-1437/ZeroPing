<?php

namespace App\Core\Events;

class EventDispatcher
{
    /**
     * @var array<string, array<int, class-string<Listener>>>
     */
    protected array $listeners = [];

    public function listen(string $event, string $listener): void
    {
        $this->listeners[$event][] = $listener;
    }

    public function dispatch(Event $event): void
    {
        $eventClass = get_class($event);

        if (!isset($this->listeners[$eventClass])) {
            return;
        }

        foreach ($this->listeners[$eventClass] as $listener) {
            (new $listener())->handle($event);
        }
    }
}
