<?php

declare(strict_types=1);

namespace App\Core\Config;

use RuntimeException;

/**
 * Environment file (.env) parser and loader.
 *
 * Parses .env files following these conventions:
 * - Lines starting with # are comments.
 * - Empty lines are ignored.
 * - Values can be unquoted, single-quoted, or double-quoted.
 * - Double-quoted values support escape sequences: \\, \", \n, \r, \t.
 * - Double-quoted values can span multiple lines.
 * - Unquoted values strip inline comments (preceded by a space then #).
 * - Variable interpolation is supported in double-quoted values: ${VAR_NAME}.
 *
 * Parsed values are stored in $_ENV and putenv() for portability.
 * A compiled cache is written to bootstrap/cache/ for fast subsequent loads.
 */
class Env
{
    /**
     * Tracks which .env paths have already been loaded this process.
     *
     * @var array<string, bool>
     */
    private static array $loaded = [];

    /**
     * Load and parse a .env file, populating $_ENV and putenv().
     *
     * Loading is idempotent: calling load() multiple times with the same
     * path is a no-op after the first successful invocation.
     *
     * @param string $path Absolute path to the .env file.
     *
     * @return void
     *
     * @throws RuntimeException If the .env file does not exist.
     */
    public static function load(string $path): void
    {
        if (isset(self::$loaded[$path])) {
            return;
        }

        if (!file_exists($path)) {
            throw new RuntimeException(".env file not found: {$path}");
        }

        self::$loaded[$path] = true;

        $cacheFile = dirname($path) . '/bootstrap/cache/env_' . md5($path) . '.php';

        // Serve from compiled cache when it exists and is not stale.
        if (file_exists($cacheFile) && filemtime($cacheFile) >= filemtime($path)) {
            $items = require $cacheFile;
            if (is_array($items)) {
                foreach ($items as $key => $value) {
                    self::setVariable($key, $value);
                }
                return;
            }
        }

        $content = file_get_contents($path);

        if ($content === false) {
            throw new RuntimeException("Failed to read .env file: {$path}");
        }

        $items = self::parse($content);

        foreach ($items as $key => $value) {
            self::setVariable($key, $value);
        }

        self::writeCache($cacheFile, $items);
    }

    /**
     * Retrieve an environment variable value.
     *
     * @param string $key     The environment variable name.
     * @param mixed  $default The default value if the variable is not set.
     *
     * @return mixed The environment variable value or the default.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $value = $_ENV[$key] ?? getenv($key);

        if ($value === false) {
            return $default;
        }

        return self::castValue((string) $value);
    }

    /**
     * Parse .env file content into an associative array.
     *
     * Handles multiline double-quoted values and all edge cases.
     *
     * @param string $content The raw file content.
     *
     * @return array<string, string> Parsed key-value pairs.
     */
    private static function parse(string $content): array
    {
        $items = [];
        $lines = explode("\n", str_replace("\r\n", "\n", $content));
        $lineCount = count($lines);
        $i = 0;

        while ($i < $lineCount) {
            $line = $lines[$i];
            $trimmed = trim($line);

            // Skip comments and empty lines.
            if ($trimmed === '' || str_starts_with($trimmed, '#')) {
                $i++;
                continue;
            }

            // Must have an = sign.
            $eqPos = strpos($trimmed, '=');
            if ($eqPos === false) {
                $i++;
                continue;
            }

            $key = trim(substr($trimmed, 0, $eqPos));
            $rawValue = substr($trimmed, $eqPos + 1);

            // Remove export prefix if present (e.g., "export FOO=bar").
            if (str_starts_with($key, 'export ')) {
                $key = trim(substr($key, 7));
            }

            if ($key === '') {
                $i++;
                continue;
            }

            $value = self::parseValue($rawValue, $lines, $i, $lineCount, $items);

            $items[$key] = $value;
            $i++;
        }

        return $items;
    }

    /**
     * Parse a single value, handling quoting, multiline, and interpolation.
     *
     * @param string                $rawValue  The raw value string after the = sign.
     * @param array<int, string>    $lines     All lines for multiline lookahead.
     * @param int                   $i         Current line index (advanced for multiline).
     * @param int                   $lineCount Total number of lines.
     * @param array<string, string> $items     Already-parsed items for interpolation.
     *
     * @return string The parsed value.
     */
    private static function parseValue(
        string $rawValue,
        array $lines,
        int &$i,
        int $lineCount,
        array $items
    ): string {
        $rawValue = ltrim($rawValue);

        if ($rawValue === '') {
            return '';
        }

        $firstChar = $rawValue[0];

        // Double-quoted value (may be multiline).
        if ($firstChar === '"') {
            $value = substr($rawValue, 1);
            $closingPos = self::findClosingQuote($value, '"');

            if ($closingPos !== false) {
                $value = substr($value, 0, $closingPos);
            } else {
                // Multiline: accumulate lines until closing quote.
                while (++$i < $lineCount) {
                    $nextLine = $lines[$i];
                    $value .= "\n" . $nextLine;
                    $closingPos = self::findClosingQuote($nextLine, '"');
                    if ($closingPos !== false) {
                        // Trim everything after the closing quote on the final line.
                        $value = substr($value, 0, strrpos($value, "\n" . $nextLine) + 1 + $closingPos);
                        break;
                    }
                }
            }

            // Process escape sequences in double-quoted values.
            $value = self::processEscapes($value);

            // Variable interpolation for double-quoted values.
            $value = self::interpolate($value, $items);

            return $value;
        }

        // Single-quoted value (literal, no escaping or interpolation).
        if ($firstChar === "'") {
            $value = substr($rawValue, 1);
            $closingPos = strpos($value, "'");

            if ($closingPos !== false) {
                return substr($value, 0, $closingPos);
            }

            // Multiline single-quoted.
            while (++$i < $lineCount) {
                $nextLine = $lines[$i];
                $value .= "\n" . $nextLine;
                $closingPos = strpos($nextLine, "'");
                if ($closingPos !== false) {
                    $value = substr($value, 0, strrpos($value, "\n" . $nextLine) + 1 + $closingPos);
                    break;
                }
            }

            return $value;
        }

        // Unquoted value: strip inline comments (space + #).
        $commentPos = strpos($rawValue, ' #');
        if ($commentPos !== false) {
            $rawValue = substr($rawValue, 0, $commentPos);
        }

        $value = trim($rawValue);

        // Variable interpolation for unquoted values.
        $value = self::interpolate($value, $items);

        return $value;
    }

    /**
     * Find the position of a closing quote, respecting escape sequences.
     *
     * @param string $str   The string to search within.
     * @param string $quote The quote character (" or ').
     *
     * @return int|false The position of the closing quote, or false if not found.
     */
    private static function findClosingQuote(string $str, string $quote): int|false
    {
        $len = strlen($str);

        for ($j = 0; $j < $len; $j++) {
            if ($str[$j] === '\\' && $quote === '"') {
                $j++; // Skip the next character (escaped).
                continue;
            }

            if ($str[$j] === $quote) {
                return $j;
            }
        }

        return false;
    }

    /**
     * Process escape sequences in double-quoted values.
     *
     * Supports: \\, \", \n, \r, \t, \$
     *
     * @param string $value The raw value with potential escape sequences.
     *
     * @return string The processed value.
     */
    private static function processEscapes(string $value): string
    {
        return str_replace(
            ['\\\\', '\\"', '\\n', '\\r', '\\t', '\\$'],
            ['\\', '"', "\n", "\r", "\t", '$'],
            $value
        );
    }

    /**
     * Interpolate ${VAR_NAME} references with previously-parsed values or $_ENV.
     *
     * @param string                $value The value containing potential interpolation tokens.
     * @param array<string, string> $items Already-parsed items in the current file.
     *
     * @return string The interpolated value.
     */
    private static function interpolate(string $value, array $items): string
    {
        return (string) preg_replace_callback('/\$\{([a-zA-Z_][a-zA-Z0-9_]*)\}/', function (array $matches) use ($items): string {
            $varName = $matches[1];

            if (isset($items[$varName])) {
                return $items[$varName];
            }

            if (isset($_ENV[$varName])) {
                return (string) $_ENV[$varName];
            }

            $envValue = getenv($varName);
            if ($envValue !== false) {
                return $envValue;
            }

            return $matches[0]; // Leave unresolved.
        }, $value);
    }

    /**
     * Cast special string values to their native types (for get() only).
     *
     * @param string $value The raw string value.
     *
     * @return mixed The cast value.
     */
    private static function castValue(string $value): mixed
    {
        return match (strtolower($value)) {
            'true', '(true)'   => true,
            'false', '(false)' => false,
            'null', '(null)'   => null,
            'empty', '(empty)' => '',
            default            => $value,
        };
    }

    /**
     * Set an environment variable in $_ENV and putenv().
     *
     * @param string $key   The variable name.
     * @param string $value The variable value.
     *
     * @return void
     */
    private static function setVariable(string $key, string $value): void
    {
        $_ENV[$key] = $value;
        putenv("{$key}={$value}");
    }

    /**
     * Write a compiled cache file for faster subsequent loads.
     *
     * @param string                $cacheFile Absolute path to the cache file.
     * @param array<string, string> $items     Parsed environment values.
     *
     * @return void
     */
    private static function writeCache(string $cacheFile, array $items): void
    {
        $dir = dirname($cacheFile);

        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }

        if (!is_writable($dir)) {
            return;
        }

        $content = '<?php return ' . var_export($items, true) . ';' . "\n";
        $tempFile = $cacheFile . '.' . uniqid('', true) . '.tmp';

        if (file_put_contents($tempFile, $content) === false) {
            return;
        }

        // Atomic rename for cache file integrity.
        if (DIRECTORY_SEPARATOR === '\\' && file_exists($cacheFile)) {
            @unlink($cacheFile);
        }

        @rename($tempFile, $cacheFile);
    }

    /**
     * Reset the loaded state (useful for testing).
     *
     * @return void
     */
    public static function reset(): void
    {
        self::$loaded = [];
    }
}
