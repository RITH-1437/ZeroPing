<?php

namespace App\Core\ORM;

class Hydrator
{
    /**
     * Hydrate a single model.
     */
    public function hydrate(
        Model $model,
        array $attributes
    ): Model {

        $instance = new ($model::class)();

        $instance->fill($attributes);

        $instance->setExists(true);

        $instance->syncOriginal();

        return $instance;
    }

    /**
     * Hydrate multiple models.
     */
    public function hydrateMany(
        Model $model,
        array $rows
    ): Collection {

        $items = [];

        foreach ($rows as $row) {

            $items[] = $this->hydrate(
                $model,
                $row
            );
        }

        return new Collection($items);
    }
}