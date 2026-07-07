<?php

namespace App\Core\ORM;

use Countable;
use IteratorAggregate;
use ArrayIterator;

class Collection implements Countable, IteratorAggregate
{
    /**
     * Collection items.
     */
    protected array $items = [];

    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    /**
     * Get all items.
     */
    public function all(): array
    {
        return $this->items;
    }

    /**
     * Get first item.
     */
    public function first(): mixed
    {
        return $this->items[0] ?? null;
    }

    /**
     * Get last item.
     */
    public function last(): mixed
    {
        return empty($this->items)
            ? null
            : $this->items[array_key_last($this->items)];
    }

    /**
     * Determine if collection is empty.
     */
    public function isEmpty(): bool
    {
        return empty($this->items);
    }

    /**
     * Determine if collection has items.
     */
    public function isNotEmpty(): bool
    {
        return !$this->isEmpty();
    }

    /**
     * Number of items.
     */
    public function count(): int
    {
        return count($this->items);
    }

    /**
     * Convert to array.
     */
    public function toArray(): array
    {
        return array_map(function ($item) {

            if ($item instanceof Model) {
                return $item->toArray();
            }

            return $item;

        }, $this->items);
    }

    /**
     * Iterator support.
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->items);
    }
}