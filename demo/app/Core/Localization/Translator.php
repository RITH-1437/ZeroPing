<?php

declare(strict_types=1);

namespace App\Core\Localization;

/**
 * Dot-key translation loader with locale fallback and :placeholder replacement.
 *
 * Language files live in resources/lang/{locale}/{file}.php and return arrays.
 *   trans('auth.failed')                 -> looks up resources/lang/<locale>/auth.php
 *   trans('welcome', ['name' => 'Ada']) -> "Welcome, Ada"
 */
class Translator
{
    protected string $locale;

    protected string $fallback;

    /**
     * @var array<string, array<string, array>>
     */
    protected array $loaded = [];

    /**
     * @param string $path
     * @param string $locale
     * @param string $fallback
     */
    public function __construct(
        protected string $path,
        string $locale = 'en',
        string $fallback = 'en'
    ) {
        $this->locale   = $locale;
        $this->fallback = $fallback;
    }

    /**
     * Get the current locale.
     *
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * Set the current locale.
     *
     * @param string $locale
     */
    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
    }

    /**
     * Get the fallback locale.
     *
     * @return string
     */
    public function getFallback(): string
    {
        return $this->fallback;
    }

    /**
     * Get a translation string by dot-notated key with optional :placeholder replacement.
     *
     * @param string $key
     * @param array $replace
     * @param string|null $locale
     * @return string
     */
    public function get(string $key, array $replace = [], ?string $locale = null): string
    {
        $locale = $locale ?? $this->locale;

        $value = $this->find($key, $locale);

        if ($value === null && $locale !== $this->fallback) {
            $value = $this->find($key, $this->fallback);
        }

        if ($value === null || is_array($value)) {
            return $key;
        }

        return $this->replace($value, $replace);
    }

    /**
     * Check if a translation key exists in the current or fallback locale.
     *
     * @param string $key
     * @param string|null $locale
     * @return bool
     */
    public function has(string $key, ?string $locale = null): bool
    {
        $locale = $locale ?? $this->locale;

        return $this->find($key, $locale) !== null
            || ($locale !== $this->fallback && $this->find($key, $this->fallback) !== null);
    }

    protected function find(string $key, string $locale): mixed
    {
        if (!str_contains($key, '.')) {
            return null;
        }

        [$file, $item] = explode('.', $key, 2);

        return $this->arrayGet($this->load($file, $locale), $item);
    }

    protected function load(string $file, string $locale): array
    {
        if (isset($this->loaded[$locale][$file])) {
            return $this->loaded[$locale][$file];
        }

        $path  = $this->path . '/' . $locale . '/' . $file . '.php';
        $lines = is_file($path) ? require $path : [];
        $lines = is_array($lines) ? $lines : [];

        $this->loaded[$locale][$file] = $lines;

        return $lines;
    }

    protected function arrayGet(array $array, string $key): mixed
    {
        if (array_key_exists($key, $array)) {
            return $array[$key];
        }

        foreach (explode('.', $key) as $segment) {
            if (!is_array($array) || !array_key_exists($segment, $array)) {
                return null;
            }

            $array = $array[$segment];
        }

        return $array;
    }

    protected function replace(string $line, array $replace): string
    {
        foreach ($replace as $key => $value) {
            $line = str_replace(':' . $key, (string) $value, $line);
        }

        return $line;
    }
}
