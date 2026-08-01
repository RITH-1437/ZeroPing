<?php

declare(strict_types=1);

namespace App\Core\ORM\Relations;

use App\Core\Database\Model;
use App\Core\ORM\Collection;

/**
 * Represents a one-to-many relationship.
 *
 * The related model stores a foreign key that references this (parent) model.
 * Calling {@see getResults()} returns all matching related records as a
 * {@see Collection}.
 *
 * Example:
 * ```php
 * // User has many Posts (posts.user_id → users.id)
 * class User extends Model
 * {
 *     public function posts(): HasMany
 *     {
 *         return $this->hasMany(Post::class);
 *     }
 * }
 *
 * $posts = $user->posts; // Collection of Post instances
 * ```
 *
 * @since 1.5.0
 */
class HasMany extends Relation
{
    /**
     * The foreign key of the parent model.
     *
     * @var string
     */
    protected string $foreignKey;

    /**
     * The local key of the parent model.
     *
     * @var string
     */
    protected string $localKey;

    /**
     * Create a new has-many relationship instance.
     *
     * @param  \App\Core\Database\Model  $parent      The owning model instance.
     * @param  \App\Core\Database\Model  $related     The related model instance.
     * @param  string                    $foreignKey  Column on the related table referencing this model.
     * @param  string                    $localKey    Column on this table (usually the primary key).
     * @since  1.5.0
     */
    public function __construct(Model $parent, Model $related, string $foreignKey, string $localKey)
    {
        $this->foreignKey = $foreignKey;
        $this->localKey = $localKey;

        parent::__construct($parent, $related);

        $this->query->where($this->foreignKey, '=', $this->parent->{$this->localKey});
    }

    /**
     * Get the foreign key on the related table.
     */
    public function getForeignKey(): string
    {
        return $this->foreignKey;
    }

    /**
     * Get the local key on the parent table.
     */
    public function getLocalKey(): string
    {
        return $this->localKey;
    }

    /**
     * Get the related model instance.
     */
    public function getRelated(): Model
    {
        return $this->related;
    }

    /**
     * Get all related model instances for this relationship.
     *
     * @return \App\Core\ORM\Collection  A collection of related model instances (may be empty).
     * @since  1.5.0
     */
    public function getResults(): Collection
    {
        return $this->query->get();
    }
}
