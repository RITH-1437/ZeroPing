<?php

declare(strict_types=1);

namespace App\Core\Testing\Traits;

use App\Core\Routing\Router;
use App\Core\Testing\TestResponse;

/**
 * Provides a lightweight HTTP test client that drives the framework's
 * Router directly (no socket/network), returning a TestResponse for
 * fluent assertions.
 */
trait MakesHTTPRequests
{
    public function get(string $uri, array $headers = []): TestResponse
    {
        return $this->call('GET', $uri, [], $headers);
    }

    public function post(string $uri, array $data = [], array $headers = []): TestResponse
    {
        return $this->call('POST', $uri, $data, $headers);
    }

    public function put(string $uri, array $data = [], array $headers = []): TestResponse
    {
        return $this->call('PUT', $uri, $data, $headers);
    }

    public function delete(string $uri, array $headers = []): TestResponse
    {
        return $this->call('DELETE', $uri, [], $headers);
    }

    public function call(string $method, string $uri, array $data = [], array $headers = []): TestResponse
    {
        $_SERVER['REQUEST_METHOD'] = strtoupper($method);
        $_SERVER['REQUEST_URI'] = $uri;

        if (in_array(strtoupper($method), ['POST', 'PUT', 'PATCH'], true)) {
            $_POST = $data;
        }

        foreach ($headers as $key => $value) {
            $_SERVER['HTTP_' . strtoupper(str_replace('-', '_', $key))] = $value;
        }

        \App\Core\Http\Response::resetLastSent();

        ob_start();
        Router::dispatch();
        $body = (string) ob_get_clean();

        $last = \App\Core\Http\Response::lastSent();
        $status = $last['status'] ?? (int) http_response_code();
        $headers = $last['headers'] ?? headers_list();

        return new TestResponse($body, $status, $headers);
    }
}
