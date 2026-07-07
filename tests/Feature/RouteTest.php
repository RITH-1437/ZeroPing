<?php

namespace Tests\Feature;

use App\Core\Testing\TestCase;

class RouteTest extends TestCase
{
    public function test_can_access_home_page()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }
}
