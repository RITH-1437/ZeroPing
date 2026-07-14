<?php

namespace App\Core\ORM\Concerns;

use App\Core\Database\Model;
use App\Core\ORM\Relations\BelongsTo;
use App\Core\ORM\Relations\BelongsToMany;
use App\Core\ORM\Relations\HasMany;
use App\Core\ORM\Relations\HasOne;

trait HasRelationships
{
    /**
     * The loaded relationships for the model.
     *
     * @var array
     */
    protected array $relations = [];

    /**
     * Define a one-to-one relationship.
     *
     * @param  string  $related
     * @param  string|null  $foreignKey
     * @param  string|null  $localKey
     * @return \App\Core\ORM\Relations\HasOne
     */
    public function hasOne(string $related, ?string $foreignKey = null, ?string $localKey = null): HasOne
    {
        $instance = new $related();
        $foreignKey = $foreignKey ?: $this->getForeignKey();
        $localKey = $localKey ?: $this->getKeyName();

        return new HasOne($this, $instance, $foreignKey, $localKey);
    }

    /**
     * Define a one-to-many relationship.
     *
     * @param  string  $related
     * @param  string|null  $foreignKey
     * @param  string|null  $localKey
     * @return \App\Core\ORM\Relations\HasMany
     */
    public function hasMany(string $related, ?string $foreignKey = null, ?string $localKey = null): HasMany
    {
        $instance = new $related();
        $foreignKey = $foreignKey ?: $this->getForeignKey();
        $localKey = $localKey ?: $this->getKeyName();

        return new HasMany($this, $instance, $foreignKey, $localKey);
    }

    /**
     * Define an inverse one-to-one or many relationship.
     *
     * @param  string  $related
     * @param  string|null  $foreignKey
     * @param  string|null  $ownerKey
     * @return \App\Core\ORM\Relations\BelongsTo
     */
    public function belongsTo(string $related, ?string $foreignKey = null, ?string $ownerKey = null): BelongsTo
    {
        $instance = new $related();
        $foreignKey = $foreignKey ?: $instance->getForeignKey();
        $ownerKey = $ownerKey ?: $instance->getKeyName();

        return new BelongsTo($this, $instance, $foreignKey, $ownerKey);
    }

    /**
     * Define a many-to-many relationship.
     *
     * @param  string  $related
     * @param  string|null  $table
     * @param  string|null  $foreignPivotKey
     * @param  string|null  $relatedPivotKey
     * @return \App\Core\ORM\Relations\BelongsToMany
     */
    public function belongsToMany(
        string $related,
        ?string $table = null,
        ?string $foreignPivotKey = null,
        ?string $relatedPivotKey = null
    ): BelongsToMany {
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
     * Get a relationship.
     *
     * @param  string  $key
     * @return mixed
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
     * Dynamically retrieve attributes on the model.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get(string $key): mixed
    {
        return $this->getAttribute($key) ?? $this->getRelationValue($key);
    }
}
