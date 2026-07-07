<?php

namespace App\Core\ORM\Concerns;

use DateTime;

trait HasAttributes
{
    /**
     * The model's attributes.
     *
     * @var array
     */
    protected array $attributes = [];

    /**
     * The model's attribute casting.
     *
     * @var array
     */
    protected array $casts = [];

    /**
     * Set a given attribute on the model.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public function setAttribute(string $key, mixed $value): void
    {
        if ($this->hasSetMutator($key)) {
            $method = 'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $key))) . 'Attribute';
            $this->{$method}($value);
        } else {
            $this->attributes[$key] = $value;
        }
    }

    /**
     * Get an attribute from the model.
     *
     * @param  string  $key
     * @return mixed
     */
    public function getAttribute(string $key): mixed
    {
        $value = $this->attributes[$key] ?? null;

        if ($this->hasGetAccessor($key)) {
            $method = 'get' . str_replace(' ', '', ucwords(str_replace('_', ' ', $key))) . 'Attribute';
            return $this->{$method}($value);
        }

        if ($this->hasCast($key)) {
            return $this->castAttribute($key, $value);
        }

        return $value;
    }

    /**
     * Determine whether an attribute should be cast to a native type.
     *
     * @param  string  $key
     * @return bool
     */
    protected function hasCast(string $key): bool
    {
        return array_key_exists($key, $this->casts);
    }

    /**
     * Cast an attribute to a native type.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    protected function castAttribute(string $key, mixed $value): mixed
    {
        $castType = $this->casts[$key];

        switch ($castType) {
            case 'int':
            case 'integer':
                return (int) $value;
            case 'real':
            case 'float':
            case 'double':
                return (float) $value;
            case 'string':
                return (string) $value;
            case 'bool':
            case 'boolean':
                return (bool) $value;
            case 'array':
            case 'json':
                return json_decode($value, true);
            case 'datetime':
                return new DateTime($value);
            default:
                return $value;
        }
    }

    /**
     * Determine if a get accessor exists for an attribute.
     *
     * @param  string  $key
     * @return bool
     */
    public function hasGetAccessor(string $key): bool
    {
        return method_exists($this, 'get' . str_replace(' ', '', ucwords(str_replace('_', ' ', $key))) . 'Attribute');
    }

    /**
     * Determine if a set mutator exists for an attribute.
     *
     * @param  string  $key
     * @return bool
     */
    public function hasSetMutator(string $key): bool
    {
        return method_exists($this, 'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $key))) . 'Attribute');
    }

    /**
     * Dynamically retrieve attributes on the model.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get(string $key): mixed
    {
        return $this->getAttribute($key);
    }

    /**
     * Dynamically set attributes on the model.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public function __set(string $key, mixed $value): void
    {
        $this->setAttribute($key, $value);
    }
}
