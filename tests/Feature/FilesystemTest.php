<?php

namespace Tests\Feature;

use App\Core\Filesystem\Storage;
use App\Core\Testing\TestCase;

class FilesystemTest extends TestCase
{
    public function test_can_put_and_get_from_storage()
    {
        Storage::put('test.txt', 'Hello World');
        $this->assertEquals('Hello World', Storage::get('test.txt'));
    }
}
