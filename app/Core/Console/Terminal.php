<?php

namespace App\Core\Console;

/**
 * Terminal helpers shared by every CLI command: terminal-width detection and a
 * single-line centering routine. Centralizing this guarantees the ASCII banner,
 * version line, taglines and links all share one horizontal center and look
 * polished on Windows Terminal, PowerShell, CMD, Linux and macOS.
 */
class Terminal
{
    /**
     * Detect the current terminal width.
     *
     * Uses the most reliable per-platform signal (the visible console window width
     * on Windows, `tput cols` on POSIX, or the COLUMNS env var), then clamps
     * the result to a sane band so output is never over-indented, shifted
     * off-screen, or wrapped on terminals of at least 80 columns.
     */
    public static function width(int $default = 80): int
    {
        static $width = null;

        if ($width !== null) {
            return $width;
        }

        $width = $default;

        $cols = getenv('COLUMNS');
        if ($cols !== false && ctype_digit($cols) && (int) $cols >= 80 && (int) $cols <= 400) {
            $width = (int) $cols;
        } elseif (function_exists('exec')) {
            if (stripos(PHP_OS, 'WIN') === 0) {
                // The visible console window width. `mode con` reports the buffer
                // width (often 120+), which would over-indent the banner.
                @exec('powershell -NoProfile -Command "try { [Console]::WindowWidth } catch { 0 }" 2>nul', $out);
                $win = (!empty($out[0]) && ctype_digit(trim((string) $out[0])))
                    ? (int) trim((string) $out[0])
                    : 0;

                // If the real window width is unavailable we keep the 80-column
                // default rather than trusting `mode con` (which reports the
                // screen buffer, often 120+, and would over-indent / wrap
                // the banner on an 80-column terminal).
                if ($win >= 80 && $win <= 400) {
                    $width = $win;
                }
            } else {
                @exec('tput cols 2>/dev/null', $out);
                if (!empty($out[0]) && ctype_digit(trim((string) $out[0]))) {
                    $width = (int) trim((string) $out[0]);
                }
            }
        }

        // Clamp to a band that keeps content centered yet always fitting on an
        // 80-column terminal without wrapping.
        if ($width < 80) {
            $width = 80;
        } elseif ($width > 200) {
            $width = 200;
        }

        return $width;
    }

    /**
     * Strip ANSI escape sequences so visible width can be measured.
     */
    public static function stripAnsi(string $line): string
    {
        return (string) preg_replace('/\e\[[0-9;]*m/', '', $line);
    }

    /**
     * Center a single (possibly ANSI-colored) line within the given width.
     *
     * The visible length is measured after stripping ANSI sequences, so color
     * codes never affect alignment.
     */
    public static function center(string $line, ?int $width = null): string
    {
        $width ??= self::width();
        $len = mb_strlen(self::stripAnsi($line));

        if ($len >= $width) {
            return $line;
        }

        $pad = (int) floor(($width - $len) / 2);

        return str_repeat(' ', $pad) . $line;
    }
}
