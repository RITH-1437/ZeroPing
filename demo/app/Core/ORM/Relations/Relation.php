<?php

namespace App\Core\ORM\Relations;

use App\Core\Database\Model;
use App\Core\Database\QueryBuilder;

abstract class Relation
{
    /**
     * The parent model instance.
     *
     * @var \App\Core\Database\Model
     */
    protected Model $parent;

    /**
     * The related model instance.
     *
     * @var \App\Core\Database\Model
     */
    protected Model $related;

    /**
     * The query builder instance.
     *
     * @var \App\Core\Database\QueryBuilder
     */
    protected QueryBuilder $query;

    /**
     * Create a new relation instance.
     *
     * @param  \App\Core\Database\Model  $parent
     * @param  \App\Core\Database\Model  $related
     * @return void
     */
    public function __construct(Model $parent, Model $related)
    {
        $this->parent = $parent;
        $this->related = $related;
        $this->query = $related->query();
    }

    /**
     * Get the results of the relationship.
     *
     * @return mixed
     */
    abstract public function getResults();
}
