<?php

namespace App\Services;

class DocsService
{
    private string $docsPath;

    /** @var array<int, array{slug:string,title:string,description:string,file:string}> */
    private array $documents = [
        [
            'slug' => 'introduction',
            'title' => 'Introduction',
            'description' => 'What ZeroPing is and why it exists.',
            'file' => 'introduction.md',
        ],
        [
            'slug' => 'installation',
            'title' => 'Installation',
            'description' => 'Install ZeroPing in minutes.',
            'file' => 'installation.md',
        ],
        [
            'slug' => 'getting-started',
            'title' => 'Getting Started',
            'description' => 'Build your first app with ZeroPing.',
            'file' => 'getting-started.md',
        ],
        [
            'slug' => 'api',
            'title' => 'API',
            'description' => 'Core APIs and facades you will use daily.',
            'file' => 'api.md',
        ],
        [
            'slug' => 'cli',
            'title' => 'CLI Reference',
            'description' => 'Complete CLI command reference for ZeroPing.',
            'file' => 'cli.md',
        ],
        [
            'slug' => 'validation',
            'title' => 'Validation',
            'description' => 'Validation rules, FluentValidator, and FormRequest.',
            'file' => 'validation.md',
        ],
        [
            'slug' => 'search',
            'title' => 'Search',
            'description' => 'Full-text documentation search with fuzzy matching.',
            'file' => 'search.md',
        ],
        [
            'slug' => 'starter-templates',
            'title' => 'Starter Templates',
            'description' => 'Scaffold projects with php zero new.',
            'file' => 'starter-templates.md',
        ],
        [
            'slug' => 'performance',
            'title' => 'Performance',
            'description' => 'Route caching, config caching, view caching, and optimization.',
            'file' => 'performance.md',
        ],
        [
            'slug' => 'roadmap',
            'title' => 'Roadmap',
            'description' => 'What is planned for ZeroPing next.',
            'file' => 'roadmap.md',
        ],
    ];

    public function __construct()
    {
        $this->docsPath = dirname(__DIR__, 2) . '/docs/website';
    }

    /**
     * @return array<int, array{slug:string,title:string,description:string,file:string}>
     */
    public function documents(): array
    {
        return $this->documents;
    }

    /**
     * @return array{slug:string,title:string,description:string,file:string}|null
     */
    public function find(string $slug): ?array
    {
        foreach ($this->documents as $doc) {
            if ($doc['slug'] === $slug) {
                return $doc;
            }
        }

        return null;
    }

    /**
     * @return array{previous:?array,next:?array}
     */
    public function neighbors(string $slug): array
    {
        $count = count($this->documents);

        foreach ($this->documents as $index => $doc) {
            if ($doc['slug'] !== $slug) {
                continue;
            }

            return [
                'previous' => $index > 0 ? $this->documents[$index - 1] : null,
                'next' => $index < $count - 1 ? $this->documents[$index + 1] : null,
            ];
        }

        return ['previous' => null, 'next' => null];
    }

    public function loadMarkdown(string $slug): ?string
    {
        $doc = $this->find($slug);

        if (!$doc) {
            return null;
        }

        $file = $this->docsPath . '/' . $doc['file'];

        if (!file_exists($file)) {
            return null;
        }

        return (string) file_get_contents($file);
    }

    /**
     * @return array{html:string,toc:array<int,array{level:int,id:string,title:string}>}
     */
    public function render(string $markdown): array
    {
        $markdown = str_replace("\r\n", "\n", $markdown);
        $lines = explode("\n", $markdown);

        $html = '';
        $toc = [];
        $paragraph = [];
        $listItems = [];
        $inCode = false;
        $codeLang = '';
        $codeLines = [];
        $codeIndex = 0;

        $flushParagraph = static function () use (&$html, &$paragraph): void {
            if ($paragraph === []) {
                return;
            }

            $text = implode(' ', array_map('trim', $paragraph));
            $html .= '<p class="mt-4 text-slate-700 dark:text-slate-300 leading-7">'
                . self::inline($text) . '</p>';
            $paragraph = [];
        };

        $flushList = static function () use (&$html, &$listItems): void {
            if ($listItems === []) {
                return;
            }

            $html .= '<ul class="mt-4 list-disc pl-6 space-y-2 '
                . 'text-slate-700 dark:text-slate-300">';
            foreach ($listItems as $item) {
                $html .= '<li>' . self::inline($item) . '</li>';
            }
            $html .= '</ul>';
            $listItems = [];
        };

        foreach ($lines as $line) {
            $trim = trim($line);

            if (str_starts_with($trim, '```')) {
                $flushParagraph();
                $flushList();

                if (!$inCode) {
                    $inCode = true;
                    $codeLang = trim(substr($trim, 3));
                    $codeLines = [];
                } else {
                    $inCode = false;
                    $codeIndex++;

                    $langClass = $codeLang !== ''
                        ? 'language-' . preg_replace('/[^a-z0-9_-]/i', '', $codeLang)
                        : 'language-txt';
                    $codeId = 'doc-code-' . $codeIndex;
                    $code = htmlspecialchars(implode("\n", $codeLines), ENT_QUOTES, 'UTF-8');

                    $html .= '<div class="relative group mt-6">';
                    $html .= '<button type="button" class="copy-code-btn absolute top-3 right-3 '
                        . 'text-xs px-2 py-1 rounded-md bg-slate-900 text-white/90 dark:bg-white '
                        . 'dark:text-slate-900" data-copy-target="' . $codeId
                        . '" aria-label="Copy code">Copy</button>';
                    $html .= '<pre class="overflow-x-auto rounded-2xl border border-slate-200 '
                        . 'bg-slate-950 p-4 text-slate-100 dark:border-slate-700">'
                        . '<code id="' . $codeId . '" class="' . $langClass . '">' . $code
                        . '</code></pre>';
                    $html .= '</div>';
                }

                continue;
            }

            if ($inCode) {
                $codeLines[] = $line;
                continue;
            }

            if (preg_match('/^(#{1,3})\s+(.+)$/', $trim, $matches)) {
                $flushParagraph();
                $flushList();

                $level = strlen($matches[1]);
                $title = trim($matches[2]);
                $id = self::slug($title);
                $escaped = htmlspecialchars(
                    $title,
                    ENT_QUOTES,
                    'UTF-8'
                );

                if ($level >= 2) {
                    $toc[] = [
                        'level' => $level,
                        'id' => $id,
                        'title' => $title,
                    ];
                }

                $class = $level === 1
                    ? 'mt-2 text-3xl font-bold tracking-tight text-slate-900 dark:text-white'
                    : ($level === 2
                        ? 'mt-10 text-2xl font-semibold text-slate-900 dark:text-white'
                        : 'mt-8 text-xl font-semibold text-slate-900 dark:text-white');

                $html .= '<h' . $level . ' id="' . $id . '" class="' . $class . '">'
                    . $escaped . '</h' . $level . '>';
                continue;
            }

            if (preg_match('/^-\s+(.+)$/', $trim, $matches)) {
                $flushParagraph();
                $listItems[] = $matches[1];
                continue;
            }

            if ($trim === '') {
                $flushParagraph();
                $flushList();
                continue;
            }

            $paragraph[] = $line;
        }

        $flushParagraph();
        $flushList();

        return ['html' => $html, 'toc' => $toc];
    }

    private static function inline(string $text): string
    {
        $escaped = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
        $escaped = preg_replace(
            '/`([^`]+)`/',
            '<code class="rounded bg-slate-100 dark:bg-slate-800 px-1 py-0.5 text-sm">$1</code>',
            $escaped
        );
        $escaped = preg_replace('/\*\*([^*]+)\*\*/', '<strong>$1</strong>', $escaped);
        $escaped = preg_replace('/\*([^*]+)\*/', '<em>$1</em>', $escaped);
        $escaped = preg_replace(
            '/\[(.*?)\]\((.*?)\)/',
            '<a href="$2" class="text-blue-700 dark:text-blue-400 hover:underline '
                . 'focus-visible:outline-none focus-visible:ring-2 '
                . 'focus-visible:ring-blue-500 rounded-sm">$1</a>',
            $escaped
        );

        return (string) $escaped;
    }

    private static function slug(string $text): string
    {
        $slug = strtolower(trim($text));
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        $slug = preg_replace('/[\s-]+/', '-', (string) $slug);

        return trim((string) $slug, '-');
    }
}
