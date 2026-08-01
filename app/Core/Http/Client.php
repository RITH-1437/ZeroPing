<?php

declare(strict_types=1);

namespace App\Core\Http;

/**
 * Fluent HTTP client for making outgoing HTTP requests.
 *
 * Wraps PHP's cURL extension with a clean, chainable API. No third-party
 * dependencies required.
 *
 * Static convenience methods are provided via __callStatic so that both of
 * the following usage patterns work without duplicate method declarations:
 *
 * @example
 *   $response = Client::get('https://api.example.com/users');
 *   $response = Client::post('https://api.example.com/users', ['name' => 'Alice']);
 *
 *   // Fluent chaining:
 *   $client = new Client();
 *   $response = $client
 *       ->withToken('my-api-token')
 *       ->withHeaders(['Accept' => 'application/json'])
 *       ->timeout(30)
 *       ->post('https://api.example.com/users', ['name' => 'Alice']);
 *
 * @method static ClientResponse get(string $url, array<string, mixed> $query = [])
 * @method static ClientResponse post(string $url, array<string, mixed> $data = [])
 * @method static ClientResponse put(string $url, array<string, mixed> $data = [])
 * @method static ClientResponse patch(string $url, array<string, mixed> $data = [])
 * @method static ClientResponse delete(string $url)
 *
 * @since 2.1.0
 */
class Client
{
    /** @var array<string, string> */
    protected array $headers = [];

    /** @var array<string, mixed> */
    protected array $options = [];

    protected int $timeout = 30;

    protected bool $verifyTls = true;

    /** @var string|null Base URL prepended to all relative paths. */
    protected ?string $baseUrl = null;

    /** @var list<string>|null Basic auth credentials [user, password]. */
    protected ?array $basicAuth = null;

    // -------------------------------------------------------------------------
    // Static proxy via __callStatic
    // -------------------------------------------------------------------------

    /**
     * Forward static calls (e.g. Client::get(...)) to a fresh instance.
     *
     * @param  string        $method
     * @param  array<mixed>  $arguments
     */
    public static function __callStatic(string $method, array $arguments): ClientResponse
    {
        $instance = new self();

        /** @var ClientResponse */
        return $instance->$method(...$arguments);
    }

    // -------------------------------------------------------------------------
    // Fluent configuration
    // -------------------------------------------------------------------------

    public function baseUrl(string $url): static
    {
        $clone = clone $this;
        $clone->baseUrl = rtrim($url, '/');
        return $clone;
    }

    /** @param array<string, string> $headers */
    public function withHeaders(array $headers): static
    {
        $clone = clone $this;
        $clone->headers = array_merge($clone->headers, $headers);
        return $clone;
    }

    public function withHeader(string $name, string $value): static
    {
        return $this->withHeaders([$name => $value]);
    }

    public function withToken(string $token, string $type = 'Bearer'): static
    {
        return $this->withHeaders(['Authorization' => $type . ' ' . $token]);
    }

    public function withBasicAuth(string $user, string $password): static
    {
        $clone = clone $this;
        $clone->basicAuth = [$user, $password];
        return $clone;
    }

    public function withoutVerifying(): static
    {
        $clone = clone $this;
        $clone->verifyTls = false;
        return $clone;
    }

    public function timeout(int $seconds): static
    {
        $clone = clone $this;
        $clone->timeout = $seconds;
        return $clone;
    }

    public function accept(string $contentType): static
    {
        return $this->withHeaders(['Accept' => $contentType]);
    }

    public function acceptJson(): static
    {
        return $this->accept('application/json');
    }

    public function contentType(string $contentType): static
    {
        return $this->withHeaders(['Content-Type' => $contentType]);
    }

    // -------------------------------------------------------------------------
    // Instance request methods
    // -------------------------------------------------------------------------

    /** @param array<string, mixed> $query */
    public function get(string $url, array $query = []): ClientResponse
    {
        return $this->request('GET', $url, ['query' => $query]);
    }

    /** @param array<string, mixed> $data */
    public function post(string $url, array $data = []): ClientResponse
    {
        return $this->request('POST', $url, ['json' => $data]);
    }

    /** @param array<string, mixed> $data */
    public function put(string $url, array $data = []): ClientResponse
    {
        return $this->request('PUT', $url, ['json' => $data]);
    }

    /** @param array<string, mixed> $data */
    public function patch(string $url, array $data = []): ClientResponse
    {
        return $this->request('PATCH', $url, ['json' => $data]);
    }

    public function delete(string $url): ClientResponse
    {
        return $this->request('DELETE', $url);
    }

    // -------------------------------------------------------------------------
    // Core request execution
    // -------------------------------------------------------------------------

    /** @param array<string, mixed> $options */
    public function request(string $method, string $url, array $options = []): ClientResponse
    {
        $queryParams = isset($options['query']) && is_array($options['query']) ? $options['query'] : [];
        $url = $this->resolveUrl($url, $queryParams);

        if ($url === '') {
            throw new \RuntimeException('URL must not be empty.');
        }

        $ch = curl_init();

        /** @var array{10002: non-empty-string, 10036: non-empty-string|null, 19913: bool, 52: bool, 68: int, 13: int, 78: int, 64: bool, ...} $curlOptions */
        $curlOptions = [
            CURLOPT_URL            => $url,
            CURLOPT_CUSTOMREQUEST  => strtoupper($method),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS      => 5,
            CURLOPT_TIMEOUT        => $this->timeout,
            CURLOPT_CONNECTTIMEOUT => min(10, $this->timeout),
            CURLOPT_SSL_VERIFYPEER => $this->verifyTls,
            CURLOPT_SSL_VERIFYHOST => $this->verifyTls ? 2 : 0,
            CURLOPT_HEADER         => true,
        ];
        curl_setopt_array($ch, $curlOptions);

        // Build headers
        $headers = array_merge(['Accept' => 'application/json'], $this->headers);

        // Attach body
        if (isset($options['json']) && is_array($options['json']) && in_array(strtoupper($method), ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            $body = json_encode($options['json']);
            if ($body !== false) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
                $headers['Content-Type'] ??= 'application/json';
                $headers['Content-Length'] = (string) strlen($body);
            }
        } elseif (isset($options['form']) && is_array($options['form'])) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($options['form']));
            $headers['Content-Type'] ??= 'application/x-www-form-urlencoded';
        }

        // Basic auth
        if ($this->basicAuth !== null) {
            $authString = implode(':', $this->basicAuth);
            if ($authString !== '') {
                curl_setopt($ch, CURLOPT_USERPWD, $authString);
            }
        }

        // Compile header array for cURL
        $curlHeaders = [];
        foreach ($headers as $name => $value) {
            $curlHeaders[] = $name . ': ' . $value;
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $curlHeaders);

        $raw        = curl_exec($ch);
        $status     = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $headerSize = (int) curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $error      = curl_error($ch);
        curl_close($ch);

        if ($raw === false || $error !== '') {
            throw new \RuntimeException('HTTP request failed: ' . $error);
        }

        $rawHeaders = substr((string) $raw, 0, $headerSize);
        $body       = substr((string) $raw, $headerSize);

        return new ClientResponse($status, $this->parseResponseHeaders($rawHeaders), $body);
    }

    // -------------------------------------------------------------------------
    // Internal helpers
    // -------------------------------------------------------------------------

    /** @param array<string, mixed> $query */
    protected function resolveUrl(string $url, array $query = []): string
    {
        if ($this->baseUrl !== null && !str_starts_with($url, 'http://') && !str_starts_with($url, 'https://')) {
            $url = $this->baseUrl . '/' . ltrim($url, '/');
        }

        if (!empty($query)) {
            $separator = str_contains($url, '?') ? '&' : '?';
            $url .= $separator . http_build_query($query);
        }

        return $url;
    }

    /** @return array<string, string> */
    protected function parseResponseHeaders(string $rawHeaders): array
    {
        $headers = [];
        foreach (explode("\r\n", $rawHeaders) as $line) {
            if (str_contains($line, ':')) {
                [$name, $value] = explode(':', $line, 2);
                $headers[strtolower(trim($name))] = trim($value);
            }
        }
        return $headers;
    }
}
