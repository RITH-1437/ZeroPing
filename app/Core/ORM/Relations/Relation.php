<?php

declare(strict_types=1);

namespace App\Core\ORM\Relations;

use App\Core\Database\Model;
use App\Core\Database\QueryBuilder;

/**
 * Base class for all ORM relationship types.
 *
 * Every concrete relation (HasOne, HasMany, BelongsTo, BelongsToMany) extends
 * this class and must implement {@see getResults()} to return the appropriate
 * model or collection.
 *
 * Example (extending for a custom relation):
 * ```php
 * class HasLatest extends Relation
 * {
 *     public function getResults(): ?Model
 *     {
 *         return $this->query->orderBy('created_at', 'desc')->first();
 *     }
 * }
 * ```
 *
 * @since 1.5.0
 */
abstract class Relation
{
    /**
     * The parent model instance.
     *
     * @var \App\Core\Database\Model
     */
    protected Model $parent;

    /**
     * The related model instance.
     *
     * @var \App\Core\Database\Model
     */
    protected Model $related;

    /**
     * The query builder instance.
     *
     * @var \App\Core\Database\QueryBuilder
     */
    protected QueryBuilder $query;

    /**
     * Create a new relation instance and initialise the base query.
     *
     * @param  \App\Core\Database\Model  $parent   The owning model instance.
     * @param  \App\Core\Database\Model  $related  The related model instance.
     * @since  1.5.0
     */
    public function __construct(Model $parent, Model $related)
    {
        $this->parent = $parent;
        $this->related = $related;
        $this->query = $related->query();
    }

    /**
     * Get the results of the relationship.
     *
     * Implementations should return a single Model instance, a Collection,
     * or null depending on the relation cardinality.
     *
     * @return mixed
     * @since  1.5.0
     */
    abstract public function getResults();
}
