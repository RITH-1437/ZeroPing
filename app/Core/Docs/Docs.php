<?php

declare(strict_types=1);

namespace App\Core\Docs;

use InvalidArgumentException;

/**
 * Minimal zero-dependency Markdown documentation renderer.
 *
 * Loads .md pages from a docs directory and converts a useful subset
 * of Markdown (headings, bold, inline code, fenced code, links,
 * unordered lists, paragraphs) into HTML.
 */
class Docs
{
    protected string $path;

    public function __construct(?string $path = null)
    {
        $this->path = $path
            ?? ((defined('BASE_PATH') ? BASE_PATH : dirname(__DIR__, 3)) . '/resources/docs');
    }

    public function has(string $page): bool
    {
        return is_file($this->path . '/' . $this->normalize($page) . '.md');
    }

    public function render(string $page): string
    {
        $file = $this->path . '/' . $this->normalize($page) . '.md';

        if (!is_file($file)) {
            throw new InvalidArgumentException("Docs page '{$page}' not found.");
        }

        return $this->toHtml((string) file_get_contents($file));
    }

    public function toHtml(string $markdown): string
    {
        $lines  = explode("\n", $markdown);
        $html   = '';
        $inList = false;
        $inCode = false;
        $code   = '';

        foreach ($lines as $line) {
            if (preg_match('/^```(\w*)\s*$/', $line) === 1) {
                if ($inCode) {
                    $html  .= '<pre><code>' . htmlspecialchars($code, ENT_QUOTES, 'UTF-8') . '</code></pre>';
                    $code  = '';
                    $inCode = false;
                } else {
                    $inCode = true;
                }

                continue;
            }

            if ($inCode) {
                $code .= $line . "\n";

                continue;
            }

            if (preg_match('/^(#{1,6})\s+(.*)$/', $line, $m) === 1) {
                $level = strlen($m[1]);

                $html .= "<h{$level}>" . $this->inline($m[2]) . "</h{$level}>";

                continue;
            }

            if (preg_match('/^\s*[-*]\s+(.*)$/', $line, $m) === 1) {
                if (!$inList) {
                    $html  .= '<ul>';
                    $inList = true;
                }

                $html .= '<li>' . $this->inline($m[1]) . '</li>';

                continue;
            }

            if ($inList) {
                $html  .= '</ul>';
                $inList = false;
            }

            if (trim($line) === '') {
                continue;
            }

            $html .= '<p>' . $this->inline($line) . '</p>';
        }

        if ($inList) {
            $html .= '</ul>';
        }

        if ($inCode) {
            $html .= '<pre><code>' . htmlspecialchars($code, ENT_QUOTES, 'UTF-8') . '</code></pre>';
        }

        return $html;
    }

    protected function inline(string $text): string
    {
        $text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
        $text = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $text);
        $text = preg_replace('/`(.+?)`/', '<code>$1</code>', $text);
        $text = preg_replace('/\[(.+?)\]\((.+?)\)/', '<a href="$2">$1</a>', $text);

        return $text;
    }

    protected function normalize(string $page): string
    {
        $normalized = preg_replace('/[^a-zA-Z0-9_.\/-]/', '', str_replace('\\', '/', $page));

        return $normalized === '' ? 'index' : ltrim($normalized, '/');
    }
}
