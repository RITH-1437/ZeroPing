<?php

namespace App\Repositories;

use App\Models\Test;

class TestRepository extends Repository
{
    public function __construct(Test $model)
    {
        $this->model = $model;
    }
}