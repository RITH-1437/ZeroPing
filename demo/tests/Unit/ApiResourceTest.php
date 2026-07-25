<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Http\Resources\AnonymousResourceCollection;
use App\Core\Http\Resources\JsonResource;
use App\Models\User;
use PHPUnit\Framework\TestCase;

final class StubUserResource extends JsonResource
{
    public function toArray(mixed $request): array
    {
        $model = is_array($this->resource)
            ? $this->resource
            : $this->resource->toArray();

        return [
            'id'   => $model['id'] ?? null,
            'name' => $model['name'] ?? null,
        ];
    }
}

final class ApiResourceTest extends TestCase
{
    public function testResolveReturnsTransformedArray(): void
    {
        $resource = StubUserResource::make(['id' => 1, 'name' => 'Ada', 'secret' => 'x']);

        $this->assertSame(['id' => 1, 'name' => 'Ada'], $resource->resolve());
    }

    public function testCollectionEnvelopsUnderDataKey(): void
    {
        $collection = StubUserResource::collection([
            ['id' => 1, 'name' => 'A'],
            ['id' => 2, 'name' => 'B'],
        ]);

        $this->assertInstanceOf(AnonymousResourceCollection::class, $collection);
        $this->assertSame(
            ['data' => [['id' => 1, 'name' => 'A'], ['id' => 2, 'name' => 'B']]],
            $collection->resolve()
        );
    }

    public function testAdditionalMergesIntoPayload(): void
    {
        $resource = StubUserResource::make(['id' => 1, 'name' => 'A'])->additional(['meta' => 'x']);

        $this->assertSame(['id' => 1, 'name' => 'A', 'meta' => 'x'], $resource->resolve());
    }

    public function testWithIsMergedAtTopLevelForCollections(): void
    {
        $collection = StubUserResource::collection([['id' => 1, 'name' => 'A']])
            ->with(['links' => ['next' => '/2']]);

        $this->assertSame(
            ['data' => [['id' => 1, 'name' => 'A']], 'links' => ['next' => '/2']],
            $collection->resolve()
        );
    }

    public function testWhenHelperConditional(): void
    {
        $resource = new class (['id' => 1]) extends JsonResource {
            public function toArray(mixed $request): array
            {
                return [
                    'id'      => $this->resource['id'],
                    'preview' => $this->when(true, 'yes', 'no'),
                    'hidden'  => $this->when(false, 'yes', 'no'),
                ];
            }
        };

        $this->assertSame(['id' => 1, 'preview' => 'yes', 'hidden' => 'no'], $resource->resolve());
    }

    public function testTransformsModelInstance(): void
    {
        $user = new User();
        $user->id = 7;
        $user->name = 'Grace';

        $this->assertSame(['id' => 7, 'name' => 'Grace'], StubUserResource::make($user)->resolve());
    }
}
