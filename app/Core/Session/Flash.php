<?php

declare(strict_types=1);

namespace App\Core\Session;

use App\Core\Auth\SessionGuard;

/**
 * Flash message manager for one-time session messages.
 *
 * Flash messages persist only until the next request retrieval,
 * making them ideal for success/error notifications after redirects.
 */
class Flash
{
    /** @var string The session key used to store flash messages. */
    private const FLASH_KEY = 'flash';

    /**
     * Set a flash message with a given type.
     *
     * @param string $type    The message type (e.g., 'success', 'error', 'warning', 'info').
     * @param string $message The message content.
     * @return void
     */
    public static function set(string $type, string $message): void
    {
        SessionGuard::set(self::FLASH_KEY, [
            'type'    => $type,
            'message' => $message,
        ]);
    }

    /**
     * Retrieve and remove the current flash message.
     *
     * @return array{type: string, message: string}|null The flash data, or null if none set.
     */
    public static function get(): ?array
    {
        $flash = SessionGuard::get(self::FLASH_KEY);

        SessionGuard::remove(self::FLASH_KEY);

        return $flash;
    }

    /**
     * Determine if a flash message is currently set.
     *
     * @return bool True if a flash message exists.
     */
    public static function has(): bool
    {
        return SessionGuard::has(self::FLASH_KEY);
    }

    /**
     * Set a success flash message.
     *
     * @param string $message The success message.
     * @return void
     */
    public static function success(string $message): void
    {
        self::set('success', $message);
    }

    /**
     * Set an error flash message.
     *
     * @param string $message The error message.
     * @return void
     */
    public static function error(string $message): void
    {
        self::set('error', $message);
    }

    /**
     * Set a warning flash message.
     *
     * @param string $message The warning message.
     * @return void
     */
    public static function warning(string $message): void
    {
        self::set('warning', $message);
    }

    /**
     * Set an informational flash message.
     *
     * @param string $message The info message.
     * @return void
     */
    public static function info(string $message): void
    {
        self::set('info', $message);
    }
}
