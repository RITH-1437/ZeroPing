<?php

namespace FrameworkSite;

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
            'slug' => 'caching',
            'title' => 'Caching',
            'description' => 'Route caching, config caching, and view caching.',
            'file' => 'caching.md',
        ],
        [
            'slug' => 'database',
            'title' => 'Database & ORM',
            'description' => 'Query builder, models, migrations, and connections.',
            'file' => 'database.md',
        ],
        [
            'slug' => 'container',
            'title' => 'Service Container',
            'description' => 'Dependency injection, bindings, and service providers.',
            'file' => 'container.md',
        ],
        [
            'slug' => 'queues',
            'title' => 'Queues & Jobs',
            'description' => 'Async job dispatching, queue drivers, and workers.',
            'file' => 'queues.md',
        ],
        [
            'slug' => 'scheduler',
            'title' => 'Task Scheduler',
            'description' => 'Cron-less scheduled tasks and interval definitions.',
            'file' => 'scheduler.md',
        ],
        [
            'slug' => 'security',
            'title' => 'Security',
            'description' => 'Encryption, hashing, CSRF, and signed URLs.',
            'file' => 'security.md',
        ],
        [
            'slug' => 'extending',
            'title' => 'Extending ZeroPing',
            'description' => 'Package development, service providers, and facades.',
            'file' => 'extending.md',
        ],
        [
            'slug' => 'starter-templates',
            'title' => 'Starter Templates',
            'description' => 'Scaffold projects with php zero new.',
            'file' => 'starter-templates.md',
        ],
        [
            'slug' => 'search',
            'title' => 'Search',
            'description' => 'Full-text documentation search with fuzzy matching.',
            'file' => 'search.md',
        ],
        [
            'slug' => 'performance',
            'title' => 'Performance',
            'description' => 'Benchmarks, profiling, and optimization tips.',
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
        $this->docsPath = dirname(__DIR__) . '/docs';
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
            $html .= '<p class="mt-4 text-zp-desc leading-7">'
                . self::inline($text) . '</p>';
            $paragraph = [];
        };

        $flushList = static function () use (&$html, &$listItems): void {
            if ($listItems === []) {
                return;
            }

            $html .= '<ul class="mt-4 list-disc pl-6 space-y-2 text-zp-desc">';
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
                    $langDisplay = $codeLang !== '' ? strtoupper(htmlspecialchars($codeLang, ENT_QUOTES, 'UTF-8')) : '';
                    $langBadge = $langDisplay
                        ? '<span class="shrink-0 rounded-full px-2.5 py-0.5 text-[10px] font-semibold uppercase tracking-wider border" style="background:#14532D;color:#BBF7D0;border-color:rgba(34,197,94,0.2);">' . $langDisplay . '</span>'
                        : '';

                    $html .= '<section class="code-block mt-6 rounded-2xl overflow-hidden shadow-xl" style="opacity:1;filter:none;backdrop-filter:none;background:#08120D;border:1px solid #1F3B2D;">';
                    $html .= '<div class="flex items-center justify-between gap-2 px-4 py-3 rounded-t-2xl" style="background:#0E1C15;border-bottom:1px solid #214233;opacity:1;">';
                    $html .= '<div class="flex items-center gap-3 min-w-0" style="opacity:1;">';
                    $html .= '<div class="flex items-center gap-1.5 shrink-0" aria-hidden="true" style="opacity:1;">';
                    $html .= '<span class="h-2.5 w-2.5 rounded-full shadow-sm" style="background:#FF5F56;opacity:1;"></span>';
                    $html .= '<span class="h-2.5 w-2.5 rounded-full shadow-sm" style="background:#FFBD2E;opacity:1;"></span>';
                    $html .= '<span class="h-2.5 w-2.5 rounded-full shadow-sm" style="background:#27C93F;opacity:1;"></span>';
                    $html .= '</div>';
                    $html .= $langBadge;
                    $html .= '</div>';
                    $html .= '<button type="button" class="copy-code-btn shrink-0 text-xs px-3 py-1 rounded-lg" style="background:#163020;color:#F8FAFC;border:1px solid #2F5A43;opacity:1;cursor:pointer;" data-copy-target="' . $codeId . '">';
                    $html .= '<svg style="height:14px;width:14px;display:inline;vertical-align:-2px;margin-right:6px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>';
                    $html .= '<span class="copy-label">Copy</span>';
                    $html .= '</button>';
                    $html .= '</div>';
                    $html .= '<pre class="overflow-x-auto" style="margin:0;border:0;border-radius:0;padding:24px;background:transparent;opacity:1;line-height:1.8;font-family:\'JetBrains Mono\',\'Fira Code\',\'Consolas\',monospace;font-size:15px;"><code id="' . $codeId . '" class="' . $langClass . '" style="opacity:1;font-weight:500;color:#F8FAFC;">' . $code . '</code></pre>';
                    $html .= '</section>';
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
                    ? 'mt-2 text-3xl font-bold tracking-tight text-zp-ink'
                    : ($level === 2
                        ? 'mt-10 text-2xl font-semibold text-zp-ink'
                        : 'mt-8 text-xl font-semibold text-zp-ink');

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
            '<code class="rounded bg-zp-muted/10 px-1 py-0.5 text-sm text-zp-ink">$1</code>',
            $escaped
        );
        $escaped = preg_replace('/\*\*([^*]+)\*\*/', '<strong>$1</strong>', $escaped);
        $escaped = preg_replace('/\*([^*]+)\*/', '<em>$1</em>', $escaped);
        $escaped = preg_replace(
            '/\[(.*?)\]\((.*?)\)/',
            '<a href="$2" class="text-zp-link hover:underline '
                . 'focus-visible:outline-none focus-visible:ring-2 '
                . 'focus-visible:ring-cyan-500 rounded-sm">$1</a>',
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
