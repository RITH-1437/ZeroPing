<?php

namespace App\Core\Console\Commands;

use App\Core\Console\Command;
use App\Core\View\View;

class ViewCacheCommand extends Command
{
    protected string $signature = 'view:cache';
    protected string $description = 'Compile all views into cached files';

    public function handle(): void
    {
        $viewsDir = BASE_PATH . '/views';
        $cacheDir = View::cachePath();

        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0777, true);
        }

        $count = 0;
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($viewsDir, \RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            $relative = str_replace($viewsDir . '/', '', $file->getPathname());
            $viewName = str_replace('.php', '', $relative);
            $viewName = str_replace('\\', '/', $viewName);

            $content = View::render($viewName, [], $this->detectLayout($viewName));

            $cacheKey = md5($viewName);
            file_put_contents($cacheDir . '/' . $cacheKey . '.php', $content);
            $count++;
        }

        $this->info("Views cached successfully! ({$count} files)");
    }

    private function detectLayout(string $view): string
    {
        if (str_starts_with($view, 'layouts/')) {
            return 'guest';
        }

        if (str_starts_with($view, 'errors/')) {
            return 'guest';
        }

        return 'site';
    }
}
