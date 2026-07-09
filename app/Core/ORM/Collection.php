<?php

namespace App\Core\ORM;

use ArrayAccess;
use ArrayIterator;
use App\Core\Contracts\Arrayable;
use App\Core\Contracts\Jsonable;
use Countable;
use IteratorAggregate;
use JsonSerializable;

class Collection implements ArrayAccess, Countable, IteratorAggregate, JsonSerializable
{
    /**
     * The items contained in the collection.
     *
     * @var array
     */
    protected array $items = [];

    /**
     * Create a new collection.
     *
     * @param  mixed  $items
     * @return void
     */
    public function __construct($items = [])
    {
        $this->items = $this->getArrayableItems($items);
    }

    /**
     * Get all of the items in the collection.
     *
     * @return array
     */
    public function all(): array
    {
        return $this->items;
    }

    /**
     * Run a map over each of the items.
     *
     * @param  callable  $callback
     * @return static
     */
    public function map(callable $callback): static
    {
        return new static(array_map($callback, $this->items));
    }

    /**
     * Run a filter over each of the items.
     *
     * @param  callable|null  $callback
     * @return static
     */
    public function filter(?callable $callback = null): static
    {
        if ($callback) {
            return new static(array_filter($this->items, $callback, ARRAY_FILTER_USE_BOTH));
        }

        return new static(array_filter($this->items));
    }

    /**
     * Filter items by the given key value pair.
     *
     * @param  string  $key
     * @param  mixed  $operator
     * @param  mixed  $value
     * @return static
     */
    public function where($key, $operator, $value = null): static
    {
        if (func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }

        return $this->filter(function ($item) use ($key, $operator, $value) {
            $retrieved = $this->data_get($item, $key);

            switch ($operator) {
                default:
                case '=':
                case '==':
                    return $retrieved == $value;
                case '!=':
                case '<>':
                    return $retrieved != $value;
                case '<':
                    return $retrieved < $value;
                case '>':
                    return $retrieved > $value;
                case '<=':
                    return $retrieved <= $value;
                case '>=':
                    return $retrieved >= $value;
                case '===':
                    return $retrieved === $value;
                case '!==':
                    return $retrieved !== $value;
            }
        });
    }

    /**
     * Get the first item from the collection passing the given truth test.
     *
     * @param  callable|null  $callback
     * @param  mixed  $default
     * @return mixed
     */
    public function first(?callable $callback = null, $default = null)
    {
        if (is_null($callback)) {
            if (empty($this->items)) {
                return $default;
            }

            return reset($this->items);
        }

        foreach ($this->items as $key => $value) {
            if ($callback($value, $key)) {
                return $value;
            }
        }

        return $default;
    }

    /**
     * Get the last item from the collection.
     *
     * @param  callable|null  $callback
     * @param  mixed  $default
     * @return mixed
     */
    public function last(?callable $callback = null, $default = null)
    {
        if (is_null($callback)) {
            return empty($this->items) ? $default : end($this->items);
        }

        return $this->reverse()->first($callback, $default);
    }

    /**
     * Get the first item by the given key value pair.
     *
     * @param  string  $key
     * @param  mixed  $operator
     * @param  mixed  $value
     * @return mixed
     */
    public function firstWhere($key, $operator, $value = null)
    {
        return $this->first($this->operatorForWhere(...func_get_args()));
    }

    /**
     * Get a single key's value from the first matching item in the collection.
     *
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function value($key, $default = null)
    {
        if ($item = $this->first()) {
            return $this->data_get($item, $key, $default);
        }

        return $default;
    }

    /**
     * Get the values of a given key.
     *
     * @param  string|array  $value
     * @param  string|null  $key
     * @return static
     */
    public function pluck($value, $key = null): static
    {
        return new static(array_column($this->items, $value, $key));
    }

    /**
     * Determine if the collection contains a given item.
     *
     * @param  mixed  $key
     * @param  mixed  $operator
     * @param  mixed  $value
     * @return bool
     */
    public function contains($key, $operator = null, $value = null): bool
    {
        if (func_num_args() > 1) {
            return $this->firstWhere(...func_get_args()) !== null;
        }

        if ($this->useAsCallable($key)) {
            return $this->first($key) !== null;
        }

        return in_array($key, $this->items);
    }

    /**
     * Sort the collection by the given key.
     *
     * @param  callable|string  $callback
     * @param  int  $options
     * @param  bool  $descending
     * @return static
     */
    public function sortBy($callback, $options = SORT_REGULAR, $descending = false): static
    {
        $results = [];

        $callback = $this->valueRetriever($callback);

        foreach ($this->items as $key => $value) {
            $results[$key] = $callback($value, $key);
        }

        $descending ? arsort($results, $options)
                    : asort($results, $options);

        foreach (array_keys($results) as $key) {
            $results[$key] = $this->items[$key];
        }

        return new static($results);
    }

    /**
     * Sort the collection in descending order by the given key.
     *
     * @param  callable|string  $callback
     * @param  int  $options
     * @return static
     */
    public function sortByDesc($callback, $options = SORT_REGULAR): static
    {
        return $this->sortBy($callback, $options, true);
    }

    /**
     * Execute a callback over each item.
     *
     * @param  callable  $callback
     * @return $this
     */
    public function each(callable $callback): static
    {
        foreach ($this->items as $key => $item) {
            if ($callback($item, $key) === false) {
                break;
            }
        }

        return $this;
    }

    /**
     * Create a collection of all elements that do not pass a given truth test.
     *
     * @param  callable|mixed  $callback
     * @return static
     */
    public function reject($callback): static
    {
        $useAsCallable = $this->useAsCallable($callback);

        return $this->filter(function ($value, $key) use ($callback, $useAsCallable) {
            return $useAsCallable
                            ? ! $callback($value, $key)
                            : $value != $callback;
        });
    }

    /**
     * Get the number of items in the collection.
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->items);
    }

    /**
     * Determine if the collection is empty or not.
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->items);
    }

    /**
     * Determine if the collection is not empty.
     *
     * @return bool
     */
    public function isNotEmpty(): bool
    {
        return ! $this->isEmpty();
    }

    /**
     * Get the collection of items as a plain array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return array_map(function ($value) {
            return $value instanceof Arrayable ? $value->toArray() : $value;
        }, $this->items);
    }

    /**
     * Get the collection of items as JSON.
     *
     * @param  int  $options
     * @return string
     */
    public function toJson($options = 0): string
    {
        return json_encode($this->jsonSerialize(), $options);
    }

    /**
     * Convert the object into something JSON serializable.
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return array_map(function ($value) {
            if ($value instanceof JsonSerializable) {
                return $value->jsonSerialize();
            } elseif ($value instanceof Arrayable) {
                return $value->toArray();
            }

            return $value;
        }, $this->items);
    }

    /**
     * Get an iterator for the items.
     *
     * @return \ArrayIterator
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->items);
    }

    /**
     * Determine if the given value is array accessible.
     *
     * @param  mixed  $value
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return array_key_exists($offset, $this->items);
    }

    /**
     * Get the value for a given offset.
     *
     * @param  mixed  $offset
     * @return mixed
     */
    public function offsetGet($offset): mixed
    {
        return $this->items[$offset];
    }

    /**
     * Set the value for a given offset.
     *
     * @param  mixed  $offset
     * @param  mixed  $value
     * @return void
     */
    public function offsetSet($offset, $value): void
    {
        if (is_null($offset)) {
            $this->items[] = $value;
        } else {
            $this->items[$offset] = $value;
        }
    }

    /**
     * Unset the value for a given offset.
     *
     * @param  mixed  $offset
     * @return void
     */
    public function offsetUnset($offset): void
    {
        unset($this->items[$offset]);
    }

    /**
     * Results array of items from Collection or Arrayable.
     *
     * @param  mixed  $items
     * @return array
     */
    protected function getArrayableItems($items): array
    {
        if (is_array($items)) {
            return $items;
        } elseif ($items instanceof self) {
            return $items->all();
        } elseif ($items instanceof Arrayable) {
            return $items->toArray();
        } elseif ($items instanceof Jsonable) {
            return json_decode($items->toJson(), true);
        } elseif ($items instanceof JsonSerializable) {
            return (array) $items->jsonSerialize();
        }

        return (array) $items;
    }
}
