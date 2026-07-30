<?php

declare(strict_types=1);

namespace App\Core\Filesystem;

use App\Core\Filesystem\Drivers\FilesystemDriver;
use App\Core\Filesystem\Drivers\LocalDriver;
use App\Core\Filesystem\Drivers\NullDriver;
use App\Core\Filesystem\Exceptions\FilesystemException;
use App\Core\Support\Config;
use InvalidArgumentException;

/**
 * Filesystem manager responsible for resolving and caching disk instances.
 *
 * Acts as a factory and registry for filesystem disks. Each disk wraps a
 * {@see FilesystemDriver} implementation inside a {@see FilesystemRepository}.
 * Disks are resolved lazily and cached for the lifetime of the manager instance.
 *
 * Custom drivers can be registered at runtime via {@see extend()}.
 */
class FilesystemManager
{
    /**
     * Resolved disk instances keyed by disk name.
     *
     * @var array<string, FilesystemRepository>
     */
    protected array $disks = [];

    /**
     * Custom driver resolvers.
     *
     * @var array<string, callable(array): FilesystemDriver>
     */
    protected array $customDrivers = [];

    /**
     * Get a filesystem disk instance.
     *
     * @param string|null $name The disk name from config, or null for the default.
     *
     * @return FilesystemRepository The resolved disk repository.
     *
     * @throws InvalidArgumentException If the disk is not configured.
     */
    public function disk(?string $name = null): FilesystemRepository
    {
        $name = $name ?? $this->getDefaultDriver();

        if (isset($this->disks[$name])) {
            return $this->disks[$name];
        }

        return $this->disks[$name] = $this->resolve($name);
    }

    /**
     * Alias for {@see disk()}.
     *
     * @param string|null $driver The disk/driver name.
     *
     * @return FilesystemRepository The resolved disk repository.
     */
    public function driver(?string $driver = null): FilesystemRepository
    {
        return $this->disk($driver);
    }

    /**
     * Register a custom filesystem driver resolver.
     *
     * @param string   $name    The driver name (used in config 'driver' key).
     * @param callable $creator A callable that receives config array and returns a FilesystemDriver.
     *
     * @return static
     */
    public function extend(string $name, callable $creator): static
    {
        $this->customDrivers[$name] = $creator;

        return $this;
    }

    /**
     * Forget a cached disk instance, forcing re-resolution on next access.
     *
     * @param string|null $name The disk name, or null to purge all.
     *
     * @return void
     */
    public function purge(?string $name = null): void
    {
        if ($name === null) {
            $this->disks = [];
        } else {
            unset($this->disks[$name]);
        }
    }

    /**
     * Get the name of the default filesystem driver/disk.
     *
     * @return string The default disk name from configuration.
     */
    public function getDefaultDriver(): string
    {
        return Config::get('filesystem.default', 'local');
    }

    /**
     * Forward method calls to the default disk.
     *
     * @param string       $method     The method name.
     * @param array<mixed> $parameters The method arguments.
     *
     * @return mixed
     */
    public function __call(string $method, array $parameters): mixed
    {
        return $this->disk()->{$method}(...$parameters);
    }

    /**
     * Resolve a disk configuration into a FilesystemRepository.
     *
     * @param string $name The disk name.
     *
     * @return FilesystemRepository The filesystem repository wrapping the driver.
     *
     * @throws InvalidArgumentException If the disk is not configured or the driver is unsupported.
     */
    protected function resolve(string $name): FilesystemRepository
    {
        $config = Config::get("filesystem.disks.{$name}");

        if (!is_array($config)) {
            throw new InvalidArgumentException("Disk [{$name}] is not configured.");
        }

        $driverName = $config['driver'] ?? $name;

        // Check custom drivers first
        if (isset($this->customDrivers[$driverName])) {
            $driver = ($this->customDrivers[$driverName])($config);

            if (!$driver instanceof FilesystemDriver) {
                throw new FilesystemException(
                    "Custom driver [{$driverName}] must return an instance of FilesystemDriver."
                );
            }

            return new FilesystemRepository($driver);
        }

        // Built-in drivers
        return match ($driverName) {
            'local' => $this->createLocalDriver($config),
            'null' => $this->createNullDriver(),
            default => throw new InvalidArgumentException("Driver [{$driverName}] is not supported."),
        };
    }

    /**
     * Create a local filesystem driver instance.
     *
     * @param array{root: string, permissions?: array} $config Driver configuration.
     *
     * @return FilesystemRepository The repository wrapping the local driver.
     */
    protected function createLocalDriver(array $config): FilesystemRepository
    {
        return new FilesystemRepository(new LocalDriver($config));
    }

    /**
     * Create a null (no-op) filesystem driver instance.
     *
     * @return FilesystemRepository The repository wrapping the null driver.
     */
    protected function createNullDriver(): FilesystemRepository
    {
        return new FilesystemRepository(new NullDriver());
    }
}
