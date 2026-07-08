<?php

namespace App\Core\Queue;

use App\Core\Database\Model;

class FailedJob extends Model
{
    protected string $table = 'failed_jobs';

    protected bool $hasSoftDeletes = false;
}
