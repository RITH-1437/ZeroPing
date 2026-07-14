<?php

namespace App\Core\ORM\Relations;

use App\Core\Database\Model;
use App\Core\ORM\Collection;

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
     * Create a new belongs to many relationship instance.
     *
     * @param  \App\Core\Database\Model  $parent
     * @param  \App\Core\Database\Model  $related
     * @param  string  $table
     * @param  string  $foreignPivotKey
     * @param  string  $relatedPivotKey
     * @return void
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
     * Get the results of the relationship.
     *
     * @return \App\Core\ORM\Collection
     */
    public function getResults(): Collection
    {
        return $this->query->get();
    }
}
