<?php

declare(strict_types=1);

namespace App\Core\Filesystem;

use App\Core\Application\App;

/**
 * Storage facade.
 *
 * Provides static-style access to the {@see FilesystemManager} through the
 * application's dependency injection container. This avoids direct instantiation
 * and ensures that the manager is always resolved from the container, respecting
 * any bindings or mocks registered for testing.
 *
 * Usage:
 *   Storage::disk('local')->put('file.txt', 'contents');
 *   Storage::put('file.txt', 'contents'); // uses default disk
 *
 * @method static FilesystemRepository disk(?string $name = null)
 * @method static FilesystemRepository driver(?string $driver = null)
 * @method static bool put(string $path, string $contents)
 * @method static string get(string $path)
 * @method static bool exists(string $path)
 * @method static bool delete(string $path)
 * @method static bool copy(string $from, string $to)
 * @method static bool move(string $from, string $to)
 * @method static int size(string $path)
 * @method static int lastModified(string $path)
 * @method static string mimeType(string $path)
 * @method static string url(string $path)
 * @method static void download(string $path, ?string $name = null, array<string, string> $headers = [])
 * @method static array<int, string> files(string $directory = '', bool $recursive = false)
 * @method static array<int, string> directories(string $directory = '', bool $recursive = false)
 * @method static bool makeDirectory(string $path)
 * @method static bool deleteDirectory(string $path)
 * @method static bool append(string $path, string $contents)
 * @method static bool prepend(string $path, string $contents)
 * @method static string getVisibility(string $path)
 * @method static bool setVisibility(string $path, string $visibility)
 *
 * @see FilesystemManager
 */
class Storage
{
    /**
     * Resolve the FilesystemManager from the DI container and forward the call.
     *
     * The manager is resolved on every call to allow the container to control
     * its lifecycle (singleton, fresh instance, or test double).
     *
     * @param string       $method    The method name being called.
     * @param array<mixed> $arguments The arguments passed to the method.
     *
     * @return mixed The return value from the FilesystemManager.
     */
    public static function __callStatic(string $method, array $arguments): mixed
    {
        $manager = App::container()->make(FilesystemManager::class);

        return $manager->{$method}(...$arguments);
    }
}
