<?php

declare(strict_types=1);

namespace App\Core\Http\Resources;

use App\Core\Http\Response;

/**
 * Wraps a list of items, each transformed by a given JsonResource class.
 *
 * The resolved payload is enveloped under a "data" key, matching the common
 * API convention, with any with()/additional() data merged at the top level.
 */
class AnonymousResourceCollection
{
    protected mixed $resource;

    protected string $collects;

    protected array $with = [];

    protected array $additional = [];

    public function __construct(mixed $resource, string $collects)
    {
        $this->resource = $resource;
        $this->collects = $collects;
    }

    public function with(array $data): static
    {
        $this->with = $data;

        return $this;
    }

    public function additional(array $data): static
    {
        $this->additional = $data;

        return $this;
    }

    protected function items(): array
    {
        $items = $this->resource;

        if ($items instanceof \Traversable) {
            $items = iterator_to_array($items);
        } elseif (is_object($items) && method_exists($items, 'all')) {
            $items = $items->all();
        }

        return is_array($items) ? $items : (array) $items;
    }

    public function resolve(mixed $request = null): array
    {
        $data = [];

        foreach ($this->items() as $item) {
            $resource = new $this->collects($item);
            $data[] = $resource->resolve($request);
        }

        return array_merge(['data' => $data], $this->with, $this->additional);
    }

    public function toResponse(): Response
    {
        return Response::json($this->resolve());
    }
}
