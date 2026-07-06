<?php

namespace App\Core\Config;

class ConfigRepository
{
    protected array $items = [];

    public function set(array $items): void
    {
        $this->items = $items;
    }

    public function all(): array
    {
        return $this->items;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $segments = explode('.', $key);

        $config = $this->items;

        foreach ($segments as $segment) {

            if (!array_key_exists($segment, $config)) {
                return $default;
            }

            $config = $config[$segment];
        }

        return $config;
    }

    public function has(string $key): bool
    {
        $segments = explode('.', $key);

        $config = $this->items;

        foreach ($segments as $segment) {
            if (!array_key_exists($segment, $config)) {
                return false;
            }

            $config = $config[$segment];
        }

        return true;
    }

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
    }
}