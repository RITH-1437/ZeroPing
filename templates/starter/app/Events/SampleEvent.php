<?php

namespace App\Events;

use App\Core\Events\Event;

/**
 * SampleEvent — illustrative domain event.
 *
 * Events let decoupled parts of the app react to something that happened.
 * Dispatch with `event(new SampleEvent($data))` and listen via a listener.
 */
class SampleEvent extends Event
{
    /**
     * @param mixed $data Payload carried by the event.
     */
    public function __construct(
        public mixed $data,
    ) {
    }
}
