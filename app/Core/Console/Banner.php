<?php

namespace App\Core\Console;

/**
 * Shared console branding for the ZeroPing Framework.
 *
 * Renders the ASCII "ZEROPING" logo (built from an explicit word definition so
 * the spelling can never drift) as a cyan -> blue -> green gradient, plus a
 * standard header block (logo, version line, taglines and repository link).
 *
 * Glyphs are authored with the plain "#" character and mapped to the block
 * "█" only at render time. This keeps every glyph row exactly 7 columns
 * wide (verifiable in source) so the logo is always a clean, rectangular
 * block that never looks jagged.
 *
 * Centering is delegated to the reusable {@see Terminal} helper so the logo,
 * version, taglines and links all share one horizontal center. Reused by
 * `php zero`, `php zero about`, `php zero doctor`, `php zero new` and the
 * install wizard so the framework presents one consistent identity.
 */
class Banner
{
    /**
     * The word spelled by the logo. Kept explicit so the banner can never
     * render as "ZERO", "NEROPING" or any other variant.
     */
    private const WORD = 'ZEROPING';

    /**
     * One space between letters keeps the glyphs legible without breaking
     * alignment on an 80-column terminal.
     */
    private const GLYPH_GAP = ' ';

    /**
     * Solid-block glyphs (6 rows x 7 columns) authored with "#".
     *
     * Every row of every letter is EXACTLY 7 characters wide, so the
     * assembled logo is a perfect 63-column rectangle. "#" is mapped to
     * the U+2588 FULL BLOCK only when the logo is rendered.
     *
     * @var array<string, string[]>
     */
    private const GLYPHS = [
        'Z' => ['#######', '   ####', '  #### ', ' ####  ', '####   ', '#######'],
        'E' => ['#######', '##     ', '##     ', '#######', '##     ', '#######'],
        'R' => ['#######', '##   ##', '#######', '## ##  ', '##  ## ', '##   ##'],
        'O' => ['#######', '##   ##', '##   ##', '##   ##', '##   ##', '#######'],
        'P' => ['#######', '##   ##', '#######', '##     ', '##     ', '##     '],
        'I' => ['#######', '  ###  ', '  ###  ', '  ###  ', '  ###  ', '#######'],
        'N' => ['##   ##', '###  ##', '## # ##', '##  ###', '##   ##', '##   ##'],
        'G' => ['#######', '##     ', '##     ', '##  ###', '##   ##', '#######'],
    ];

    /**
     * Cyan -> blue -> green gradient, mapped per logo row (top to bottom).
     *
     * @var string[]
     */
    private const GRADIENT = [
        "\e[36m",
        "\e[36m",
        "\e[34m",
        "\e[34m",
        "\e[32m",
        "\e[32m",
    ];

    private const BLOCK = '█';

    private const RESET = "\e[0m";

    private const REPO_URL = 'https://github.com/RITH-1437/ZeroPing';

    /**
     * Map the "#" authoring character to the solid block glyph.
     */
    protected static function toBlock(string $line): string
    {
        return str_replace('#', self::BLOCK, $line);
    }

    /**
     * Build the raw (uncolored) logo rows for the configured word.
     *
     * @return string[]
     */
    protected static function logoLines(): array
    {
        $rows = [];
        $letters = str_split(self::WORD);
        $glyphRows = count(self::GLYPHS['Z']);

        for ($r = 0; $r < $glyphRows; $r++) {
            $line = '';

            foreach ($letters as $i => $ch) {
                $line .= self::toBlock(self::GLYPHS[$ch][$r]);
                if ($i < count($letters) - 1) {
                    $line .= self::GLYPH_GAP;
                }
            }

            $rows[] = $line;
        }

        return $rows;
    }

    /**
     * Return the gradient ASCII logo as a centered, multi-line string.
     *
     * The whole block is centered by its own measured width (not per row) so
     * every glyph row shares one horizontal center and the banner never
     * looks jagged.
     */
    public static function logo(int $width = null): string
    {
        $width ??= Terminal::width();
        $lines = self::logoLines();

        $bannerWidth = 0;
        foreach ($lines as $line) {
            $bannerWidth = max($bannerWidth, mb_strlen(Terminal::stripAnsi($line)));
        }

        $pad = $bannerWidth >= $width ? 0 : (int) floor(($width - $bannerWidth) / 2);

        $out = [];
        foreach ($lines as $i => $line) {
            $color = self::GRADIENT[$i] ?? self::RESET;
            $out[] = $color . str_repeat(' ', $pad) . $line . self::RESET;
        }

        return implode("\n", $out);
    }

    /**
     * Return the full branded header block:
     *
     *   <logo>
     *
     *   ZeroPing Framework vX.Y.Z
     *
     *   Lightweight PHP Framework
     *   Fast • Elegant • Extensible
     *
     *   https://github.com/RITH-1437/ZeroPing
     */
    public static function header(string $version, string $url = self::REPO_URL): string
    {
        $width = Terminal::width();

        return implode("\n", [
            self::logo($width),
            '',
            Terminal::center("\e[1;37mZeroPing Framework v" . $version . self::RESET, $width),
            '',
            Terminal::center("\e[90mLightweight PHP Framework" . self::RESET, $width),
            Terminal::center("\e[90mFast • Elegant • Extensible" . self::RESET, $width),
            '',
            Terminal::center("\e[36m" . $url . self::RESET, $width),
        ]);
    }
}
