<?php

namespace App\Core\Testing;

use App\Core\Testing\Traits\MakesHTTPRequests;

abstract class TestCase
{
    use Assertion, MakesHTTPRequests;
}
