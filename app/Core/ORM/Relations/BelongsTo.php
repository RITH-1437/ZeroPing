<?php

declare(strict_types=1);

namespace App\Core\ORM\Relations;

use App\Core\Database\Model;

/**
 * Represents an inverse one-to-one or many-to-one relationship.
 *
 * This model stores the foreign key that points to the related (owner) model.
 * Calling {@see getResults()} returns the single owning record or null.
 *
 * Example:
 * ```php
 * // Post belongs to User (posts.user_id → users.id)
 * class Post extends Model
 * {
 *     public function author(): BelongsTo
 *     {
 *         return $this->belongsTo(User::class, 'user_id');
 *     }
 * }
 *
 * $author = $post->author; // User|null
 * ```
 *
 * @since 1.5.0
 */
class BelongsTo extends Relation
{
    /**
     * The foreign key of the parent model.
     *
     * @var string
     */
    protected string $foreignKey;

    /**
     * The associated key on the parent model.
     *
     * @var string
     */
    protected string $ownerKey;

    /**
     * Create a new belongs-to relationship instance.
     *
     * @param  \App\Core\Database\Model  $parent      The child model instance (stores the foreign key).
     * @param  \App\Core\Database\Model  $related     The owning model instance.
     * @param  string                    $foreignKey  Column on this (child) table (e.g. `user_id`).
     * @param  string                    $ownerKey    Column on the related (owner) table (usually the primary key).
     * @since  1.5.0
     */
    public function __construct(Model $parent, Model $related, string $foreignKey, string $ownerKey)
    {
        $this->foreignKey = $foreignKey;
        $this->ownerKey = $ownerKey;

        parent::__construct($parent, $related);

        $this->query->where($this->ownerKey, '=', $this->parent->{$this->foreignKey});
    }

    /**
     * Get the foreign key on this (child) model.
     */
    public function getForeignKey(): string
    {
        return $this->foreignKey;
    }

    /**
     * Get the owner key on the related (parent) table.
     */
    public function getOwnerKey(): string
    {
        return $this->ownerKey;
    }

    /**
     * Get the related model instance.
     */
    public function getRelated(): Model
    {
        return $this->related;
    }

    /**
     * Get the owning model instance for this relationship.
     *
     * @return \App\Core\Database\Model|null  The owner model, or null if the foreign key has no match.
     * @since  1.5.0
     */
    public function getResults(): ?Model
    {
        return $this->query->first();
    }
}
