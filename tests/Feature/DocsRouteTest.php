<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Core\Routing\Router;
use App\Core\Testing\TestCase;

class DocsRouteTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Router::get('/docs/{page}', fn($page) => (new \App\Core\Docs\Docs())->render($page));
    }

    public function testDocsRouteRendersPage(): void
    {
        $response = $this->get('/docs/introduction');

        $response->assertOk();
        $response->assertSee('ZeroPing');
        $response->assertSee('Installation');
    }

    public function testDocsRouteRendersReleaseNotes(): void
    {
        $response = $this->get('/docs/release-2.0.0-beta');

        $response->assertOk();
        $response->assertSee('2.0.0-beta');
    }

    public function testUnknownDocsPageReturns500(): void
    {
        $response = $this->get('/docs/missing-page');

        $response->assertStatus(500);
    }
}
