<?php

namespace App\Core\Testing\Traits;

use App\Core\Testing\Database\DatabaseAssertions;
use App\Core\Testing\Database\RefreshDatabase;
use App\Core\Testing\Database\Transaction;

trait InteractsWithDatabase
{
    use DatabaseAssertions;
    use RefreshDatabase;
    use Transaction;
}
