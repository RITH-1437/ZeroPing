<?php

declare(strict_types=1);

namespace App\Core\Config;

/**
 * Repository for storing and retrieving configuration values.
 *
 * Supports dot-notation for nested key access (e.g., "app.name").
 * Internally maintains a flat resolution cache for frequently accessed
 * keys to avoid repeated array traversal on hot paths.
 */
class ConfigRepository
{
    /**
     * The raw configuration items (nested array tree).
     *
     * @var array<string, mixed>
     */
    protected array $items = [];

    /**
     * Resolved key => value cache to avoid repeated dot-notation walks.
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
     * Replace all configuration items.
     *
     * Invalidates the internal resolution caches.
     *
     * @param array<string, mixed> $items The full configuration array tree.
     *
     * @return void
     */
    public function set(array $items): void
    {
        $this->items = $items;
        $this->cache = [];
        $this->existsCache = [];
    }

    /**
     * Return all configuration items as a nested array.
     *
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return $this->items;
    }

    /**
     * Retrieve a configuration value using dot notation.
     *
     * Example: $repo->get('database.connections.mysql.host')
     *
     * @param string $key     The dot-notated key path.
     * @param mixed  $default The value to return if the key does not exist.
     *
     * @return mixed The resolved configuration value.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        if (array_key_exists($key, $this->cache)) {
            return $this->cache[$key];
        }

        $segments = explode('.', $key);
        $config = $this->items;

        foreach ($segments as $segment) {
            if (!is_array($config) || !array_key_exists($segment, $config)) {
                $this->cache[$key] = $default;
                return $default;
            }

            $config = $config[$segment];
        }

        $this->cache[$key] = $config;
        return $config;
    }

    /**
     * Determine if a configuration key exists using dot notation.
     *
     * Returns true even if the value is null, as long as the key path
     * is fully resolvable within the items tree.
     *
     * @param string $key The dot-notated key path.
     *
     * @return bool True if the key exists in the configuration.
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
            if (!is_array($config) || !array_key_exists($segment, $config)) {
                $exists = false;
                break;
            }

            $config = $config[$segment];
        }

        $this->existsCache[$key] = $exists;
        return $exists;
    }

    /**
     * Set a configuration value using dot notation.
     *
     * Creates intermediate arrays as needed. Invalidates all caches to
     * ensure subsequent reads reflect the new value.
     *
     * @param string $key   The dot-notated key path.
     * @param mixed  $value The value to set.
     *
     * @return void
     */
    public function setValue(string $key, mixed $value): void
    {
        $segments = explode('.', $key);
        $config = &$this->items;

        foreach ($segments as $segment) {
            if (!isset($config[$segment]) || !is_array($config[$segment])) {
                $config[$segment] = [];
            }

            $config = &$config[$segment];
        }

        $config = $value;

        // Invalidate memoization; the key may now resolve differently.
        $this->cache = [];
        $this->existsCache = [];
    }
}
