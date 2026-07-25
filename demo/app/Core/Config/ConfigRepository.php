<?php

namespace App\Core\Config;

class ConfigRepository
{
    protected array $items = [];

    /**
     * Resolved key => value cache. Config is read many times per request
     * (often the same keys), so memoizing avoids repeated explode() walks.
     *
     * @var array<string, mixed>
     */
    protected array $cache = [];

    /**
     * Resolved key => existence cache (separate from $cache so that keys
     * whose value is null still report as existing).
     *
     * @var array<string, bool>
     */
    protected array $existsCache = [];

    /**
     * Replace all config items.
     *
     * @param array $items
     */
    public function set(array $items): void
    {
        $this->items = $items;
        $this->cache = [];
        $this->existsCache = [];
    }

    /**
     * Return all config items.
     *
     * @return array
     */
    public function all(): array
    {
        return $this->items;
    }

    /**
     * Retrieve a config value using dot notation.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        if (array_key_exists($key, $this->cache)) {
            return $this->cache[$key];
        }

        $segments = explode('.', $key);

        $config = $this->items;

        foreach ($segments as $segment) {
            if (!array_key_exists($segment, $config)) {
                $this->cache[$key] = $default;
                return $default;
            }

            $config = $config[$segment];
        }

        $this->cache[$key] = $config;
        return $config;
    }

    /**
     * Determine if a config key exists using dot notation.
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        if (array_key_exists($key, $this->existsCache)) {
            return $this->existsCache[$key];
        }

        $segments = explode('.', $key);

        $config = $this->items;
        $exists = true;

        foreach ($segments as $segment) {
            if (!array_key_exists($segment, $config)) {
                $exists = false;
                break;
            }

            $config = $config[$segment];
        }

        $this->existsCache[$key] = $exists;
        return $exists;
    }

    /**
     * Set a config value using dot notation.
     *
     * @param string $key
     * @param mixed $value
     */
    public function setValue(string $key, mixed $value): void
    {
        $segments = explode('.', $key);

        $config =& $this->items;

        foreach ($segments as $segment) {
            if (!isset($config[$segment])) {
                $config[$segment] = [];
            }

            $config =& $config[$segment];
        }

        $config = $value;

        // Invalidate the memoization; the key may now resolve differently.
        $this->cache = [];
        $this->existsCache = [];
    }
}
