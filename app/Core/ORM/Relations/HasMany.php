<?php

namespace App\Core\ORM\Relations;

use App\Core\Database\Model;
use App\Core\ORM\Collection;

class HasMany extends Relation
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
     * Create a new has many relationship instance.
     *
     * @param  \App\Core\Database\Model  $parent
     * @param  \App\Core\Database\Model  $related
     * @param  string  $foreignKey
     * @param  string  $localKey
     * @return void
     */
    public function __construct(Model $parent, Model $related, string $foreignKey, string $localKey)
    {
        $this->foreignKey = $foreignKey;
        $this->localKey = $localKey;

        parent::__construct($parent, $related);

        $this->query->where($this->foreignKey, '=', $this->parent->{$this->localKey});
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
