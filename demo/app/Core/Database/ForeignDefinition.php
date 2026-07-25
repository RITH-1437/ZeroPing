<?php

namespace App\Core\Database;

/**
 * Fluent builder returned by Blueprint::foreign().
 *
 * Example:
 *   $table->foreign('user_id')->references('id')->on('users')->onDelete('CASCADE');
 */
class ForeignDefinition
{
    public function __construct(
        private Blueprint $blueprint,
        private string $column
    ) {
    }

    public function references(string $column): static
    {
        $this->blueprint->addForeignCommand($this->column, 'referencesColumn', $column);

        return $this;
    }

    public function on(string $table): static
    {
        $this->blueprint->addForeignCommand($this->column, 'referencesTable', $table);

        return $this;
    }

    public function onDelete(string $action): static
    {
        $this->blueprint->addForeignCommand($this->column, 'onDelete', $action);

        return $this;
    }

    public function onUpdate(string $action): static
    {
        $this->blueprint->addForeignCommand($this->column, 'onUpdate', $action);

        return $this;
    }
}
