<?php

namespace App\Core\ORM\Concerns;

trait SoftDeletes
{
    /**
     * Indicates if the model is currently force deleting.
     *
     * @var bool
     */
    protected bool $forceDeleting = false;

    /**
     * The name of the "deleted at" column.
     *
     * @var string
     */
    const DELETED_AT = 'deleted_at';

    /**
     * Boot the soft deleting trait for a model.
     *
     * @return void
     */
    public static function bootSoftDeletes(): void
    {
        static::addGlobalScope('softDeletes', function ($query) {
            $query->whereNull(static::DELETED_AT);
        });
    }

    /**
     * Force a hard delete on a soft deleted model.
     *
     * @return bool|null
     */
    public function forceDelete(): ?bool
    {
        $this->forceDeleting = true;

        return $this->delete();
    }

    /**
     * Perform the actual delete query on this model instance.
     *
     * @return bool
     */
    protected function performDeleteOnModel(): bool
    {
        if ($this->forceDeleting) {
            return $this->query()->where('id', $this->getKey())->forceDelete();
        }

        return $this->runSoftDelete();
    }

    /**
     * Perform the soft delete query on this model instance.
     *
     * @return bool
     */
    protected function runSoftDelete(): bool
    {
        $query = $this->query()->where('id', $this->getKey());

        $time = $this->freshTimestamp();

        $columns = [static::DELETED_AT => $time];

        if ($this->timestamps) {
            $this->setAttribute(static::UPDATED_AT, $time);
            $columns[static::UPDATED_AT] = $time;
        }

        return $query->update($columns);
    }

    /**
     * Restore a soft-deleted model instance.
     *
     * @return bool|null
     */
    public function restore(): ?bool
    {
        if ($this->fireModelEvent('restoring') === false) {
            return false;
        }

        $this->setAttribute(static::DELETED_AT, null);

        $this->fireModelEvent('restored', false);

        return $this->save();
    }

    /**
     * Determine if the model instance has been soft-deleted.
     *
     * @return bool
     */
    public function trashed(): bool
    {
        return ! is_null($this->{static::DELETED_AT});
    }

    /**
     * Get a new query builder that includes soft deletes.
     *
     * @return \App\Core\Database\QueryBuilder
     */
    public static function withTrashed(): \App\Core\Database\QueryBuilder
    {
        return (new static())->query()->withTrashed();
    }

    /**
     * Get a new query builder that only includes soft deletes.
     *
     * @return \App\Core\Database\QueryBuilder
     */
    public static function onlyTrashed(): \App\Core\Database\QueryBuilder
    {
        return (new static())->query()->onlyTrashed();
    }
}
