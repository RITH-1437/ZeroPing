<?php

namespace App\Core\ORM;

use App\Core\Database\Database;
use App\Core\Database\QueryBuilder;

class Persister
{
    /**
     * Save the model.
     */
    public function save(Model $model): bool
    {
        if ($model->exists()) {
            return $this->update($model);
        }

        return $this->insert($model);
    }

    /**
     * Insert a new model.
     */
    public function insert(Model $model): bool
    {
        $query = new QueryBuilder(
            Database::connect(),
            $model->getTable()
        );

        $result = $query->insert(
            $model->getAttributes()
        );

        if ($result) {
            $model->setExists(true);

            $model->syncOriginal();
        }

        return $result;
    }

    /**
     * Update an existing model.
     */
    public function update(Model $model): bool
    {
        $query = new QueryBuilder(
            Database::connect(),
            $model->getTable()
        );

        $primaryKey = $model->getPrimaryKey();

        $attributes = $model->getAttributes();

        $result = $query
            ->where(
                $primaryKey,
                $attributes[$primaryKey]
            )
            ->update($attributes);

        if ($result) {
            $model->syncOriginal();
        }

        return $result;
    }

    /**
     * Delete the model.
     */
    public function delete(Model $model): bool
    {
        $query = new QueryBuilder(
            Database::connect(),
            $model->getTable()
        );

        $primaryKey = $model->getPrimaryKey();

        $attributes = $model->getAttributes();

        return $query
            ->where(
                $primaryKey,
                $attributes[$primaryKey]
            )
            ->delete();
    }
}
