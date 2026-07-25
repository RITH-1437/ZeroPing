<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Testing\TestResponse;
use PHPUnit\Framework\TestCase;

class TestResponseTest extends TestCase
{
    private function response(string $body = '', int $status = 200, array $headers = []): TestResponse
    {
        return new TestResponse($body, $status, $headers);
    }

    public function test_assert_status_passes_for_matching_code(): void
    {
        $response = $this->response('', 200);

        $this->assertSame($response, $response->assertStatus(200));
    }

    public function test_assert_status_throws_for_mismatch(): void
    {
        $this->expectException(\PHPUnit\Framework\AssertionFailedError::class);

        $this->response('', 404)->assertStatus(200);
    }

    public function test_assert_ok_and_not_found(): void
    {
        $this->response('', 200)->assertOk();
        $this->response('', 404)->assertNotFound();
    }

    public function test_assert_json_parses_fragment(): void
    {
        $response = $this->response((string) json_encode(['ok' => true, 'name' => 'Ada']));

        $response->assertJson(['ok' => true]);
        $response->assertJson(['name' => 'Ada']);
    }

    public function test_assert_json_throws_on_invalid_json(): void
    {
        $this->expectException(\PHPUnit\Framework\AssertionFailedError::class);

        $this->response('not json')->assertJson(['a' => 1]);
    }

    public function test_assert_see_and_header(): void
    {
        $response = $this->response(
            '<h1>Hello</h1>',
            302,
            ['Location: /login', 'X-Framework: ZeroPing']
        );

        $response->assertSee('Hello');
        $response->assertHeader('Location', '/login');
        $response->assertRedirect('/login');
    }

    public function test_assert_header_throws_when_missing(): void
    {
        $this->expectException(\PHPUnit\Framework\AssertionFailedError::class);

        $this->response('', 200, [])->assertHeader('X-Missing');
    }

    public function test_json_helper_returns_decoded_array(): void
    {
        $data = $this->response((string) json_encode(['a' => 1]))->json();

        $this->assertSame(['a' => 1], $data);
    }
}
