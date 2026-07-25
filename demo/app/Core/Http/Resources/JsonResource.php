<?php

declare(strict_types=1);

namespace App\Core\Http\Resources;

use App\Core\Http\Response;

/**
 * Base class for API resources.
 *
 * Transform a model or array into a structured API representation that can be
 * returned directly from a route or controller (it is automatically rendered
 * to JSON by the router's response handling).
 */
abstract class JsonResource
{
    protected mixed $resource;

    protected array $with = [];

    protected array $additional = [];

    /**
     * @param mixed $resource
     */
    public function __construct(mixed $resource)
    {
        $this->resource = $resource;
    }

    /**
     * Transform the resource into an array.
     *
     * @param mixed $request
     * @return array
     */
    abstract public function toArray(mixed $request): array;

    /**
     * @param mixed $resource
     * @return static
     */
    public static function make(mixed $resource): static
    {
        return new static($resource);
    }

    /**
     * Create a collection resource wrapping each item in this resource class.
     *
     * @param mixed $resource
     * @return AnonymousResourceCollection
     */
    public static function collection(mixed $resource): AnonymousResourceCollection
    {
        return new AnonymousResourceCollection($resource, static::class);
    }

    /**
     * Add top-level key/value pairs to the outgoing response.
     *
     * @param array $data
     * @return static
     */
    public function with(array $data): static
    {
        $this->with = $data;

        return $this;
    }

    /**
     * Merge extra data into the resource payload.
     *
     * @param array $data
     * @return static
     */
    public function additional(array $data): static
    {
        $this->additional = $data;

        return $this;
    }

    /**
     * Conditionally include a value (Laravel-style helper).
     */
    protected function when(bool $condition, mixed $value, mixed $default = null): mixed
    {
        return $condition ? $value : $default;
    }

    /**
     * Resolve the resource to its final array form.
     *
     * @param mixed|null $request
     * @return array
     */
    public function resolve(mixed $request = null): array
    {
        return array_merge($this->toArray($request), $this->additional);
    }

    /**
     * Build an HTTP response for this resource.
     *
     * @return Response
     */
    public function toResponse(): Response
    {
        return Response::json($this->resolve());
    }
}
