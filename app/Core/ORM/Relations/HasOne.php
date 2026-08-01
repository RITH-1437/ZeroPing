<?php

declare(strict_types=1);

namespace App\Core\ORM\Relations;

use App\Core\Database\Model;

/**
 * Represents a one-to-one relationship.
 *
 * The related model stores a foreign key that references this (parent) model.
 * Calling {@see getResults()} returns the first matching related record or
 * null when none exists.
 *
 * Example:
 * ```php
 * // User has one Profile (profiles.user_id → users.id)
 * class User extends Model
 * {
 *     public function profile(): HasOne
 *     {
 *         return $this->hasOne(Profile::class);
 *     }
 * }
 *
 * $profile = $user->profile; // Profile|null
 * ```
 *
 * @since 1.5.0
 */
class HasOne extends Relation
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
     * Create a new has-one relationship instance.
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
     * Get the single related model instance for this relationship.
     *
     * @return \App\Core\Database\Model|null  The related model, or null if none exists.
     * @since  1.5.0
     */
    public function getResults(): ?Model
    {
        return $this->query->first();
    }
}
