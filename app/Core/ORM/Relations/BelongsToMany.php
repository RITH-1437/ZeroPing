<?php

declare(strict_types=1);

namespace App\Core\ORM\Relations;

use App\Core\Database\Model;
use App\Core\ORM\Collection;

/**
 * Represents a many-to-many relationship via a pivot (intermediate) table.
 *
 * Both the parent and related model reference each other through a join table
 * whose name is derived alphabetically from the two model names when not
 * supplied explicitly (e.g. `role_user` for `User` ↔ `Role`).
 *
 * Example:
 * ```php
 * // User belongs to many Roles (via role_user pivot)
 * class User extends Model
 * {
 *     public function roles(): BelongsToMany
 *     {
 *         return $this->belongsToMany(Role::class);
 *         // or explicitly: $this->belongsToMany(Role::class, 'role_user', 'user_id', 'role_id')
 *     }
 * }
 *
 * $roles = $user->roles; // Collection of Role instances
 * ```
 *
 * @since 1.5.0
 */
class BelongsToMany extends Relation
{
    /**
     * The intermediate table for the relation.
     *
     * @var string
     */
    protected string $table;

    /**
     * The foreign key of the parent model.
     *
     * @var string
     */
    protected string $foreignPivotKey;

    /**
     * The foreign key of the related model.
     *
     * @var string
     */
    protected string $relatedPivotKey;

    /**
     * Create a new belongs-to-many relationship instance.
     *
     * @param  \App\Core\Database\Model  $parent           The owning model instance.
     * @param  \App\Core\Database\Model  $related          The related model instance.
     * @param  string                    $table            Name of the pivot table.
     * @param  string                    $foreignPivotKey  Pivot column referencing the parent model.
     * @param  string                    $relatedPivotKey  Pivot column referencing the related model.
     * @since  1.5.0
     */
    public function __construct(
        Model $parent,
        Model $related,
        string $table,
        string $foreignPivotKey,
        string $relatedPivotKey
    ) {
        $this->table = $table;
        $this->foreignPivotKey = $foreignPivotKey;
        $this->relatedPivotKey = $relatedPivotKey;

        parent::__construct($parent, $related);

        $this->query->join(
            $this->table,
            $this->related->getTable() . '.id',
            '=',
            $this->table . '.' . $this->relatedPivotKey
        )
            ->where($this->table . '.' . $this->foreignPivotKey, '=', $this->parent->id);
    }

    /**
     * Get the pivot/intermediate table name.
     */
    public function getPivotTable(): string
    {
        return $this->table;
    }

    /**
     * Get the foreign pivot key (parent's FK on the pivot table).
     */
    public function getForeignPivotKey(): string
    {
        return $this->foreignPivotKey;
    }

    /**
     * Get the related pivot key (related model's FK on the pivot table).
     */
    public function getRelatedPivotKey(): string
    {
        return $this->relatedPivotKey;
    }

    /**
     * Get the related model instance.
     */
    public function getRelated(): Model
    {
        return $this->related;
    }

    /**
     * Get all related model instances for this many-to-many relationship.
     *
     * @return \App\Core\ORM\Collection  A collection of related model instances (may be empty).
     * @since  1.5.0
     */
    public function getResults(): Collection
    {
        return $this->query->get();
    }
}
