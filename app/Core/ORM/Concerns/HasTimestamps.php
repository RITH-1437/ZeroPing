<?php

namespace App\Core\ORM\Concerns;

trait HasTimestamps
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public bool $timestamps = true;

    /**
     * The name of the "created at" column.
     *
     * @var string
     */
    const CREATED_AT = 'created_at';

    /**
     * The name of the "updated at" column.
     *
     * @var string
     */
    const UPDATED_AT = 'updated_at';

    /**
     * Update the model's timestamps.
     *
     * @return void
     */
    protected function updateTimestamps(): void
    {
        $time = date('Y-m-d H:i:s');

        if (!isset($this->attributes[static::CREATED_AT])) {
            $this->setAttribute(static::CREATED_AT, $time);
        }

        $this->setAttribute(static::UPDATED_AT, $time);
    }

    /**
     * Get a fresh timestamp for the model.
     *
     * @return string
     */
    public function freshTimestamp(): string
    {
        return date('Y-m-d H:i:s');
    }
}
