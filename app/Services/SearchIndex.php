<?php

namespace App\Services;

class SearchIndex
{
    private DocsService $docs;

    private string $cachePath;

    private ?array $index = null;

    public function __construct()
    {
        $this->docs = new DocsService();
        $this->cachePath = dirname(__DIR__, 2) . '/bootstrap/cache/search.php';
    }

    public function build(?string $docsPath = null): array
    {
        $index = [];

        $documents = $this->docs->documents();

        if ($docsPath !== null) {
            return $this->buildFromDirectory($docsPath);
        }

        foreach ($documents as $doc) {
            $markdown = $this->docs->loadMarkdown($doc['slug']);
            if ($markdown === null) {
                continue;
            }

            $rendered = $this->docs->render($markdown);
            $text = strip_tags($rendered['html']);

            $sentences = preg_split('/(?<=[.?!])\s+(?=[A-Z])/', $text, -1, PREG_SPLIT_NO_EMPTY);

            $chunks = [];
            $current = '';

            foreach ($sentences as $sentence) {
                if (strlen($current) + strlen($sentence) > 500) {
                    if ($current !== '') {
                        $chunks[] = trim($current);
                    }
                    $current = $sentence;
                } else {
                    $current .= ' ' . $sentence;
                }
            }

            if ($current !== '') {
                $chunks[] = trim($current);
            }

            foreach ($chunks as $chunk) {
                $index[] = [
                    'slug' => $doc['slug'],
                    'title' => $doc['title'],
                    'content' => $chunk,
                    'url' => '/docs/' . $doc['slug'],
                ];
            }
        }

        $this->write($index);

        return $index;
    }

    public function search(string $query, int $limit = 10): array
    {
        $index = $this->load();

        if ($index === []) {
            return [];
        }

        $query = mb_strtolower(trim($query));

        if ($query === '') {
            return [];
        }

        $terms = preg_split('/\s+/', $query);

        $scored = [];

        foreach ($index as $entry) {
            $titleLower = mb_strtolower($entry['title']);
            $contentLower = mb_strtolower($entry['content']);

            $score = 0;

            foreach ($terms as $term) {
                $termScore = $this->fuzzyScore($term, $titleLower, $contentLower);
                $score += $termScore;

                $exactTitle = mb_substr_count($titleLower, $term);
                $score += $exactTitle * 10;

                $exactContent = mb_substr_count($contentLower, $term);
                $score += $exactContent * 2;
            }

            if ($score > 0) {
                $scored[] = [
                    'slug' => $entry['slug'],
                    'title' => $entry['title'],
                    'content' => $this->highlight($entry['content'], $terms),
                    'url' => $entry['url'],
                    'score' => $score,
                ];
            }
        }

        usort($scored, fn(array $a, array $b) => $b['score'] <=> $a['score']);

        return array_slice($scored, 0, $limit);
    }

    private function fuzzyScore(string $term, string $titleLower, string $contentLower): int
    {
        if ($term === '') {
            return 0;
        }

        if (str_contains($titleLower, $term)) {
            return 20;
        }

        if (str_contains($contentLower, $term)) {
            return 5;
        }

        $titleDistance = $this->levenshteinPrefix($term, $titleLower, 3);
        if ($titleDistance !== null) {
            return max(0, 10 - $titleDistance * 3);
        }

        $contentDistance = $this->levenshteinPrefix($term, $contentLower, 2);
        if ($contentDistance !== null) {
            return max(0, 3 - $contentDistance);
        }

        return 0;
    }

    private function levenshteinPrefix(string $term, string $haystack, int $maxDistance): ?int
    {
        $termLen = strlen($term);
        $minDistance = null;

        $words = preg_split('/\s+/', $haystack);

        foreach ($words as $word) {
            $word = trim($word);
            if ($word === '') {
                continue;
            }

            $distance = levenshtein($term, mb_substr($word, 0, $termLen));

            if ($distance <= $maxDistance) {
                if ($minDistance === null || $distance < $minDistance) {
                    $minDistance = $distance;
                }
            }
        }

        return $minDistance;
    }

    private function highlight(string $content, array $terms): string
    {
        foreach ($terms as $term) {
            if ($term === '') {
                continue;
            }

            $pattern = '/(' . preg_quote($term, '/') . ')/iu';

            $content = preg_replace(
                $pattern,
                '<mark class="bg-blue-500/20 text-blue-600 dark:text-blue-400 rounded px-0.5">$1</mark>',
                $content
            );
        }

        $maxLength = 300;

        if (mb_strlen($content) > $maxLength) {
            $pos = mb_strpos($content, '<mark');
            if ($pos === false) {
                $pos = 0;
            }
            $start = max(0, $pos - 100);
            $content = ($start > 0 ? '...' : '') . mb_substr($content, $start, $maxLength) . '...';
        }

        return $content;
    }

    private function buildFromDirectory(string $dir): array
    {
        $files = glob($dir . '/*.md');
        $index = [];

        foreach ($files as $file) {
            $content = file_get_contents($file);
            $text = strip_tags((new DocsService())->render($content)['html']);
            $slug = pathinfo($file, PATHINFO_FILENAME);

            preg_match('/^#\s+(.+)$/m', $content, $titleMatch);
            $title = $titleMatch[1] ?? $slug;

            $index[] = [
                'slug' => $slug,
                'title' => $title,
                'content' => $text,
                'url' => '/docs/' . $slug,
            ];
        }

        return $index;
    }

    private function load(): array
    {
        if ($this->index !== null) {
            return $this->index;
        }

        if (file_exists($this->cachePath)) {
            $this->index = (array) include $this->cachePath;
            return $this->index;
        }

        $this->index = $this->build();

        return $this->index;
    }

    private function write(array $index): void
    {
        $dir = dirname($this->cachePath);

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        file_put_contents(
            $this->cachePath,
            '<?php return ' . var_export($index, true) . ';'
        );
    }
}
