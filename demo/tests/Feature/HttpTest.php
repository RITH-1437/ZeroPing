<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Core\Http\Response;
use App\Core\Routing\Router;
use App\Core\Testing\TestCase;

class HttpTest extends TestCase
{
    public function test_get_returns_json_response(): void
    {
        Router::get('/_zp_ping', static fn() => Response::json(['ok' => true]));

        $this->get('/_zp_ping')
            ->assertOk()
            ->assertJson(['ok' => true]);
    }

    public function test_post_receives_body_and_redirects(): void
    {
        Router::post('/_zp_echo', static function () {
            return Response::json(['received' => $_POST['name'] ?? null]);
        });

        $this->post('/_zp_echo', ['name' => 'Ada'])
            ->assertOk()
            ->assertJson(['received' => 'Ada']);

        Router::get('/_zp_login', static fn() => new Response('', 302, ['Location' => '/dashboard']));

        $this->get('/_zp_login')
            ->assertStatus(302)
            ->assertRedirect('/dashboard')
            ->assertHeader('Location', '/dashboard');
    }

    public function test_html_response_assert_see(): void
    {
        Router::get('/_zp_home', static fn() => new Response('<h1>Welcome</h1>'));

        $this->get('/_zp_home')
            ->assertOk()
            ->assertSee('Welcome');
    }
}
