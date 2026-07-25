<?php

namespace App\Repositories;

use App\Core\Database\Repository;
use App\Models\Coffee;

class CoffeeRepository extends Repository
{
    public function __construct(Coffee $model)
    {
        $this->model = $model;
    }
}
