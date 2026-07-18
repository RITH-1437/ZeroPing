<?php

namespace App\Services;

/**
 * SampleService — illustrative service class.
 *
 * Services hold reusable business logic that doesn't belong in a controller
 * or model. Inject them via the container or instantiate directly.
 */
class SampleService
{
    public function __construct()
    {
        //
    }

    /**
     * A sample business operation.
     */
    public function greet(string $name): string
    {
        return 'Hello, ' . $name . '!';
    }
}
