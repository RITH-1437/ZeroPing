<?php

namespace App\Core\Testing\HTTP;

trait HTTPAssertions
{
    public function get(string $uri, array $headers = []): TestResponse
    {
        return $this->call('GET', $uri, [], [], [], $headers);
    }

    public function post(string $uri, array $data = [], array $headers = []): TestResponse
    {
        return $this->call('POST', $uri, $data, [], [], $headers);
    }

    public function put(string $uri, array $data = [], array $headers = []): TestResponse
    {
        return $this->call('PUT', $uri, $data, [], [], $headers);
    }

    public function delete(string $uri, array $data = [], array $headers = []): TestResponse
    {
        return $this->call('DELETE', $uri, $data, [], [], $headers);
    }

    public function patch(string $uri, array $data = [], array $headers = []): TestResponse
    {
        return $this->call('PATCH', $uri, $data, [], [], $headers);
    }

    public function call(string $method, string $uri, array $parameters = [], array $cookies = [], array $files = [], array $server = [], string $content = ''): TestResponse
    {
        $request = new TestRequest($method, $uri, $parameters, $cookies, $files, $server, $content);
        return $request->send();
    }
}
