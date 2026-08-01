<?php

declare(strict_types=1);

namespace App\Core\ORM\Concerns;

use App\Core\Database\Model;
use App\Core\ORM\Relations\BelongsTo;
use App\Core\ORM\Relations\BelongsToMany;
use App\Core\ORM\Relations\HasMany;
use App\Core\ORM\Relations\HasOne;

/**
 * Provides relationship definition methods for ORM models.
 *
 * Include this trait in any Model subclass to gain `hasOne`, `hasMany`,
 * `belongsTo`, and `belongsToMany` factory methods, as well as lazy-loaded
 * relationship access via magic `__get`.
 *
 * Example:
 * ```php
 * class User extends Model
 * {
 *     use HasRelationships;
 *
 *     public function posts(): HasMany  { return $this->hasMany(Post::class); }
 *     public function profile(): HasOne { return $this->hasOne(Profile::class); }
 * }
 * ```
 *
 * @since 1.5.0
 */
trait HasRelationships
{
    /**
     * The loaded (already-resolved) relationships keyed by relation name.
     *
     * @var array<string, mixed>
     */
    protected array $relations = [];

    /**
     * Define a one-to-one relationship.
     *
     * The related model must store the foreign key that points back to this
     * model's local key (defaults to the primary key).
     *
     * @param  class-string  $related    Fully-qualified class name of the related model.
     * @param  string|null   $foreignKey Column on the related table (default: `{parent}_id`).
     * @param  string|null   $localKey   Column on this table (default: primary key).
     * @return \App\Core\ORM\Relations\HasOne
     * @since  1.5.0
     */
    public function hasOne(string $related, ?string $foreignKey = null, ?string $localKey = null): HasOne
    {
        /** @var \App\Core\Database\Model $instance */
        $instance = new $related();
        $foreignKey = $foreignKey ?: $this->getForeignKey();
        $localKey = $localKey ?: $this->getKeyName();

        return new HasOne($this, $instance, $foreignKey, $localKey);
    }

    /**
     * Define a one-to-many relationship.
     *
     * Returns a collection of related model instances whose foreign key
     * matches this model's local key.
     *
     * @param  class-string  $related    Fully-qualified class name of the related model.
     * @param  string|null   $foreignKey Column on the related table (default: `{parent}_id`).
     * @param  string|null   $localKey   Column on this table (default: primary key).
     * @return \App\Core\ORM\Relations\HasMany
     * @since  1.5.0
     */
    public function hasMany(string $related, ?string $foreignKey = null, ?string $localKey = null): HasMany
    {
        /** @var \App\Core\Database\Model $instance */
        $instance = new $related();
        $foreignKey = $foreignKey ?: $this->getForeignKey();
        $localKey = $localKey ?: $this->getKeyName();

        return new HasMany($this, $instance, $foreignKey, $localKey);
    }

    /**
     * Define an inverse one-to-one or many relationship.
     *
     * This model stores the foreign key that references the related model's
     * owner key (defaults to the related model's primary key).
     *
     * @param  class-string  $related    Fully-qualified class name of the related model.
     * @param  string|null   $foreignKey Column on this table (default: `{related}_id`).
     * @param  string|null   $ownerKey   Column on the related table (default: primary key).
     * @return \App\Core\ORM\Relations\BelongsTo
     * @since  1.5.0
     */
    public function belongsTo(string $related, ?string $foreignKey = null, ?string $ownerKey = null): BelongsTo
    {
        /** @var \App\Core\Database\Model $instance */
        $instance = new $related();
        $foreignKey = $foreignKey ?: $this->getForeignKey();
        $ownerKey = $ownerKey ?: $instance->getKeyName();

        return new BelongsTo($this, $instance, $foreignKey, $ownerKey);
    }

    /**
     * Define a many-to-many relationship via a pivot table.
     *
     * The pivot table name is derived alphabetically from the two model names
     * when not supplied explicitly (e.g. `role_user` for `User` ↔ `Role`).
     *
     * @param  class-string  $related          Fully-qualified class name of the related model.
     * @param  string|null   $table            Pivot table name (auto-derived when omitted).
     * @param  string|null   $foreignPivotKey  Pivot column for this model (default: `{parent}_id`).
     * @param  string|null   $relatedPivotKey  Pivot column for the related model (default: `{related}_id`).
     * @return \App\Core\ORM\Relations\BelongsToMany
     * @since  1.5.0
     */
    public function belongsToMany(
        string $related,
        ?string $table = null,
        ?string $foreignPivotKey = null,
        ?string $relatedPivotKey = null
    ): BelongsToMany {
        /** @var \App\Core\Database\Model $instance */
        $instance = new $related();
        $table = $table ?: $this->getJoinTable($instance);
        $foreignPivotKey = $foreignPivotKey ?: $this->getForeignKey();
        $relatedPivotKey = $relatedPivotKey ?: $instance->getForeignKey();

        return new BelongsToMany($this, $instance, $table, $foreignPivotKey, $relatedPivotKey);
    }

    /**
     * Get the foreign key for the model.
     *
     * @return string
     */
    public function getForeignKey(): string
    {
        return strtolower(basename(str_replace('\\', '/', static::class)))
            . '_id';
    }

    /**
     * Get the primary key for the model.
     *
     * @return string
     */
    public function getKeyName(): string
    {
        return 'id';
    }

    /**
     * Get the table name for a pivot table.
     *
     * @param  \App\Core\Database\Model  $related
     * @return string
     */
    protected function getJoinTable(Model $related): string
    {
        $models = [
            strtolower(basename(str_replace('\\', '/', static::class))),
            strtolower(basename(str_replace('\\', '/', get_class($related))))
        ];

        sort($models);

        return implode('_', $models);
    }

    /**
     * Get a relationship value, resolving it lazily on first access.
     *
     * Returns the cached result if the relationship was already loaded,
     * otherwise calls the relationship method and caches the result.
     *
     * @param  string  $key  Relationship name (must match a public method on this model).
     * @return mixed         The related model, collection, or null if not found.
     * @since  1.5.0
     */
    public function getRelationValue(string $key)
    {
        if (array_key_exists($key, $this->relations)) {
            return $this->relations[$key];
        }

        if (method_exists($this, $key)) {
            return $this->relations[$key] = $this->$key()->getResults();
        }

        return null;
    }

    /**
     * Set a loaded relationship on the model.
     *
     * @param  string  $name   Relation name.
     * @param  mixed   $value  The related model(s) to cache.
     * @return $this
     */
    public function setRelation(string $name, mixed $value): static
    {
        $this->relations[$name] = $value;

        return $this;
    }

    /**
     * Get all loaded relations keyed by name.
     *
     * @return array<string, mixed>
     */
    public function getRelations(): array
    {
        return $this->relations;
    }

    /**
     * Dynamically retrieve a model attribute or lazy-load a relationship.
     *
     * Attribute access is attempted first; if no attribute is found the key
     * is treated as a relationship name and resolved via {@see getRelationValue}.
     *
     * @param  string  $key  Attribute or relationship name.
     * @return mixed
     * @since  1.5.0
     */
    public function __get(string $key): mixed
    {
        return $this->getAttribute($key) ?? $this->getRelationValue($key);
    }
}
