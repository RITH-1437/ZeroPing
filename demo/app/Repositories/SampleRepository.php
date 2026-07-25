<?php

namespace App\Repositories;

use App\Core\Database\Repository;
use App\Models\Sample;

/**
 * SampleRepository — illustrative repository.
 *
 * Repositories abstract data access behind a clean interface so controllers
 * depend on behaviour, not SQL. Extend App\Core\Database\Repository.
 */
class SampleRepository extends Repository
{
    public function __construct(Sample $model)
    {
        $this->model = $model;
    }
}
