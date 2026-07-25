<?php

namespace App\Core\Database;

use App\Core\Database\Model;

abstract class Repository
{
    protected Model $model;

    public function all(): mixed
    {
        return $this->model->all();
    }

    public function find(int $id): ?array
    {
        return $this->model->query()
            ->where('id', $id)
            ->first();
    }

    public function create(array $data): bool
    {
        $this->model->create($data);

        return true;
    }

    public function update(int $id, array $data): bool
    {
        $record = $this->model->find($id);

        if (!$record) {
            return false;
        }

        return $record->update($data);
    }

    public function delete(int $id): bool
    {
        $record = $this->model->find($id);

        if (!$record) {
            return false;
        }

        return $record->delete();
    }
}
