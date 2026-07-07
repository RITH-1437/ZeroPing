<?php

namespace App\Core\ORM\Concerns;

use App\Core\ORM\Exceptions\MassAssignmentException;

trait GuardsAttributes
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected array $fillable = [];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected array $guarded = ['*'];

    /**
     * Fill the model with an array of attributes.
     *
     * @param  array  $attributes
     * @return $this
     *
     * @throws \App\Core\ORM\Exceptions\MassAssignmentException
     */
    public function fill(array $attributes): self
    {
        foreach ($this->filterFillable($attributes) as $key => $value) {
            if ($this->isFillable($key)) {
                $this->setAttribute($key, $value);
            } else {
                throw new MassAssignmentException(sprintf(
                    'Cannot mass assign attribute [%s] on model [%s].',
                    $key,
                    static::class
                ));
            }
        }

        return $this;
    }

    /**
     * Filter the given attributes against the fillable attributes.
     *
     * @param  array  $attributes
     * @return array
     */
    protected function filterFillable(array $attributes): array
    {
        if (count($this->fillable) > 0) {
            return array_intersect_key($attributes, array_flip($this->fillable));
        }

        return $attributes;
    }

    /**
     * Determine if the given attribute may be mass assigned.
     *
     * @param  string  $key
     * @return bool
     */
    public function isFillable(string $key): bool
    {
        if (in_array($key, $this->fillable)) {
            return true;
        }

        if ($this->isGuarded($key)) {
            return false;
        }

        return empty($this->fillable) && ! str_starts_with($key, '_');
    }

    /**
     * Determine if the given key is guarded.
     *
     * @param  string  $key
     * @return bool
     */
    public function isGuarded(string $key): bool
    {
        if (empty($this->guarded)) {
            return false;
        }

        return $this->guarded === ['*'] || in_array($key, $this->guarded);
    }
}
