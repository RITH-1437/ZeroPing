<?php

namespace App\Core\Testing;

use App\Core\Testing\Traits\MakesHTTPRequests;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    use Assertion;
    use MakesHTTPRequests;
}
