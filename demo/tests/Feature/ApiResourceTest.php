<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Core\Application\App;
use App\Core\Http\Kernel;
use App\Core\Http\Resources\JsonResource;
use App\Core\Routing\Router;
use PHPUnit\Framework\TestCase;

final class FeatureUserResource extends JsonResource
{
    public function toArray(mixed $request): array
    {
        return ['id' => $this->resource['id'], 'name' => $this->resource['name']];
    }
}

class ApiResourceTest extends TestCase
{
    public function testRouteReturningResourceRendersJson(): void
    {
        $uri = '/api/users/' . uniqid();

        Router::get($uri, function () {
            return FeatureUserResource::make(['id' => 42, 'name' => 'Ada', 'password' => 'secret']);
        });

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI']   = $uri;

        $kernel = new Kernel(new App(BASE_PATH));

        ob_start();
        $kernel->handle();
        $output = (string) ob_get_clean();

        $this->assertJson($output);
        $decoded = json_decode($output, true);
        $this->assertSame(['id' => 42, 'name' => 'Ada'], $decoded);
    }

    public function testRouteReturningResourceCollectionEnvelopsData(): void
    {
        $uri = '/api/users/' . uniqid();

        Router::get($uri, function () {
            return FeatureUserResource::collection([
                ['id' => 1, 'name' => 'A'],
                ['id' => 2, 'name' => 'B'],
            ]);
        });

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI']   = $uri;

        $kernel = new Kernel(new App(BASE_PATH));

        ob_start();
        $kernel->handle();
        $output = (string) ob_get_clean();

        $decoded = json_decode($output, true);
        $this->assertSame(
            ['data' => [['id' => 1, 'name' => 'A'], ['id' => 2, 'name' => 'B']]],
            $decoded
        );
    }
}
