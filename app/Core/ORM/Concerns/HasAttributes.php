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
     * Memoized accessor/mutator method existence, keyed by class then key.
     * getAttribute()/setAttribute() call method_exists() + string building on
     * every access; caching it removes that overhead in hot loops/templates.
     *
     * @var array<class-string, array<string, bool>>
     */
    private static array $accessorCache = [];

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

        // Keep the primary key consistently typed as an integer so that a
        // model hydrated from the database (where PDO may return a string)
        // matches one created in-process (which stores an int).
        if ($key === $this->getKeyName() && is_numeric($value)) {
            $this->attributes[$key] = (int) $value;
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
        return $this->cachedMutator('get', $key);
    }

    /**
     * Determine if a set mutator exists for an attribute.
     *
     * @param  string  $key
     * @return bool
     */
    public function hasSetMutator(string $key): bool
    {
        return $this->cachedMutator('set', $key);
    }

    /**
     * Memoize "<type>Attribute" method existence per (class, key).
     */
    private function cachedMutator(string $type, string $key): bool
    {
        $class = static::class;

        if (!isset(self::$accessorCache[$class])) {
            self::$accessorCache[$class] = [];
        }

        if (array_key_exists($key, self::$accessorCache[$class])) {
            return self::$accessorCache[$class][$key];
        }

        $method = $type . str_replace(' ', '', ucwords(str_replace('_', ' ', $key))) . 'Attribute';
        $exists = method_exists($this, $method);

        self::$accessorCache[$class][$key] = $exists;

        return $exists;
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
