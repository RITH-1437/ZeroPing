<?php

declare(strict_types=1);

namespace App\Core\Packages;

/**
 * Immutable representation of the package enable/disable configuration.
 *
 * Reads from `config/packages.php` and provides query methods. The core
 * design is immutable (value object pattern): mutation methods like
 * {@see withEnabled()}, {@see withDisabled()}, and {@see without()} return
 * new instances rather than modifying state.
 *
 * For backward compatibility, convenience methods {@see enable()},
 * {@see disable()}, and {@see remove()} are provided that create a new
 * instance and immediately persist it to disk.
 */
final class PackageConfig
{
    /**
     * The package enable/disable map.
     *
     * @var array<string, bool>
     */
    private readonly array $packages;

    /**
     * The absolute base path to the project root.
     *
     * @var string
     */
    private readonly string $basePath;

    /**
     * Create a new PackageConfig instance.
     *
     * @param string             $basePath The absolute project root path.
     * @param array<string, bool>|null $packages Pre-loaded package map (null to load from disk).
     */
    public function __construct(string $basePath, ?array $packages = null)
    {
        $this->basePath = $basePath;
        $this->packages = $packages ?? $this->loadFromDisk();
    }

    /**
     * Get all package configurations.
     *
     * @return array<string, bool> Package names mapped to their enabled state.
     */
    public function all(): array
    {
        return $this->packages;
    }

    /**
     * Determine if a package is declared in the configuration.
     *
     * @param string $name The package name (e.g. "zeroping/auth").
     *
     * @return bool True if the package is listed (regardless of enabled state).
     */
    public function has(string $name): bool
    {
        return array_key_exists($name, $this->packages);
    }

    /**
     * Determine if a package is enabled.
     *
     * @param string $name The package name.
     *
     * @return bool True if the package is explicitly enabled.
     */
    public function isEnabled(string $name): bool
    {
        return ($this->packages[$name] ?? false) === true;
    }

    /**
     * Return a new instance with the specified package set to the given state.
     *
     * This method does NOT mutate the current instance or write to disk.
     *
     * @param string $name    The package name.
     * @param bool   $enabled Whether the package should be enabled.
     *
     * @return static A new PackageConfig instance with the change applied.
     */
    public function withPackage(string $name, bool $enabled): static
    {
        $packages = $this->packages;
        $packages[$name] = $enabled;

        return new static($this->basePath, $packages);
    }

    /**
     * Return a new instance with the specified package enabled.
     *
     * This method does NOT mutate the current instance or write to disk.
     *
     * @param string $name The package name to enable.
     *
     * @return static A new PackageConfig instance.
     */
    public function withEnabled(string $name): static
    {
        return $this->withPackage($name, true);
    }

    /**
     * Return a new instance with the specified package disabled.
     *
     * This method does NOT mutate the current instance or write to disk.
     *
     * @param string $name The package name to disable.
     *
     * @return static A new PackageConfig instance.
     */
    public function withDisabled(string $name): static
    {
        return $this->withPackage($name, false);
    }

    /**
     * Return a new instance with the specified package removed.
     *
     * This method does NOT mutate the current instance or write to disk.
     *
     * @param string $name The package name to remove.
     *
     * @return static A new PackageConfig instance without the package entry.
     */
    public function without(string $name): static
    {
        $packages = $this->packages;
        unset($packages[$name]);

        return new static($this->basePath, $packages);
    }

    /**
     * Enable a package and persist immediately to disk.
     *
     * Reads the current state from disk before applying the change, so
     * sequential calls accumulate correctly even though this instance is
     * immutable.
     *
     * @param string $name The package name to enable.
     *
     * @return void
     */
    public function enable(string $name): void
    {
        (new static($this->basePath))->withEnabled($name)->persist();
    }

    /**
     * Disable a package and persist immediately to disk.
     *
     * Reads the current state from disk before applying the change.
     *
     * @param string $name The package name to disable.
     *
     * @return void
     */
    public function disable(string $name): void
    {
        (new static($this->basePath))->withDisabled($name)->persist();
    }

    /**
     * Remove a package from config and persist immediately to disk.
     *
     * Reads the current state from disk before applying the change.
     *
     * @param string $name The package name to remove.
     *
     * @return void
     */
    public function remove(string $name): void
    {
        (new static($this->basePath))->without($name)->persist();
    }

    /**
     * Persist the current configuration state to disk.
     *
     * Writes the package map back to `config/packages.php`.
     *
     * @return bool True on success, false on failure.
     */
    public function persist(): bool
    {
        $lines = "<?php\n\nreturn [\n";

        foreach ($this->packages as $key => $value) {
            $escapedKey = addslashes($key);
            $lines .= "    '{$escapedKey}' => " . ($value ? 'true' : 'false') . ",\n";
        }

        $lines .= "];\n";

        $result = file_put_contents($this->getPath(), $lines, LOCK_EX);

        return $result !== false;
    }

    /**
     * Get the number of configured packages.
     *
     * @return int The total number of entries.
     */
    public function count(): int
    {
        return count($this->packages);
    }

    /**
     * Get only the enabled packages.
     *
     * @return array<string, bool> Packages where the value is true.
     */
    public function enabled(): array
    {
        return array_filter($this->packages, static fn(bool $v): bool => $v);
    }

    /**
     * Get only the disabled packages.
     *
     * @return array<string, bool> Packages where the value is false.
     */
    public function disabled(): array
    {
        return array_filter($this->packages, static fn(bool $v): bool => !$v);
    }

    /**
     * Get the absolute path to the packages configuration file.
     *
     * @return string The file path.
     */
    private function getPath(): string
    {
        return $this->basePath . '/config/packages.php';
    }

    /**
     * Load the package configuration from disk.
     *
     * Returns an empty array if the file does not exist or contains
     * non-array data.
     *
     * @return array<string, bool> The loaded package map.
     */
    private function loadFromDisk(): array
    {
        $file = $this->getPath();

        if (!is_file($file)) {
            return [];
        }

        try {
            $data = require $file;
        } catch (\Throwable) {
            return [];
        }

        if (!is_array($data)) {
            return [];
        }

        // Normalize: ensure all values are booleans
        $normalized = [];
        foreach ($data as $key => $value) {
            if (is_string($key) && $key !== '') {
                $normalized[$key] = (bool) $value;
            }
        }

        return $normalized;
    }
}
