<?php

namespace App\Core\ORM\Relations;

use App\Core\Database\Model;

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
     * Create a new belongs to relationship instance.
     *
     * @param  \App\Core\Database\Model  $parent
     * @param  \App\Core\Database\Model  $related
     * @param  string  $foreignKey
     * @param  string  $ownerKey
     * @return void
     */
    public function __construct(Model $parent, Model $related, string $foreignKey, string $ownerKey)
    {
        $this->foreignKey = $foreignKey;
        $this->ownerKey = $ownerKey;

        parent::__construct($parent, $related);

        $this->query->where($this->ownerKey, '=', $this->parent->{$this->foreignKey});
    }

    /**
     * Get the results of the relationship.
     *
     * @return \App\Core\Database\Model|null
     */
    public function getResults(): ?Model
    {
        return $this->query->first();
    }
}
