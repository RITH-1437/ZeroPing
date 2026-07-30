<?php

declare(strict_types=1);

namespace App\Core\Logging;

/**
 * PSR-3 compliant log level constants.
 *
 * Defines the eight standard log levels as specified by RFC 5424 and PSR-3.
 * Levels are ordered by severity from most critical (emergency) to least (debug).
 *
 * @see https://www.php-fig.org/psr/psr-3/
 * @see https://tools.ietf.org/html/rfc5424
 */
class LogLevel
{
    /**
     * System is unusable.
     */
    public const EMERGENCY = 'emergency';

    /**
     * Action must be taken immediately.
     */
    public const ALERT = 'alert';

    /**
     * Critical conditions.
     */
    public const CRITICAL = 'critical';

    /**
     * Runtime errors that do not require immediate action.
     */
    public const ERROR = 'error';

    /**
     * Exceptional occurrences that are not errors.
     */
    public const WARNING = 'warning';

    /**
     * Normal but significant events.
     */
    public const NOTICE = 'notice';

    /**
     * Interesting events.
     */
    public const INFO = 'info';

    /**
     * Detailed debug information.
     */
    public const DEBUG = 'debug';

    /**
     * All valid PSR-3 log levels ordered by severity (highest first).
     *
     * @var array<int, string>
     */
    public const ALL = [
        self::EMERGENCY,
        self::ALERT,
        self::CRITICAL,
        self::ERROR,
        self::WARNING,
        self::NOTICE,
        self::INFO,
        self::DEBUG,
    ];

    /**
     * Determine if a given level string is a valid PSR-3 log level.
     *
     * @param string $level The level to validate.
     *
     * @return bool True if valid.
     */
    public static function isValid(string $level): bool
    {
        return in_array(strtolower($level), self::ALL, true);
    }
}
