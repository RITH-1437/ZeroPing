<?php

namespace App\Core\ORM\Concerns;

use App\Core\Event;

trait HasEvents
{
    /**
     * The event map for the model.
     *
     * @var array
     */
    protected array $dispatchesEvents = [];

    /**
     * Fire the given event for the model.
     *
     * @param  string  $event
     * @param  bool  $halt
     * @return mixed
     */
    protected function fireModelEvent(string $event, bool $halt = true)
    {
        if (! isset($this->dispatchesEvents[$event])) {
            return true;
        }

        $result = Event::dispatch("model.{$event}: " . static::class, $this);

        if ($halt && ! is_null($result)) {
            return $result;
        }

        return true;
    }
}
