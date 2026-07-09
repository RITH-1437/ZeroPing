<?php

namespace App\Core\Console\Commands;

class NewCommand
{
    private array $templates = [
        'empty' => 'Minimal project skeleton with a single welcome page',
        'blog'  => 'Blog with posts, categories, and pagination',
        'api'   => 'RESTful API with authentication boilerplate',
        'mvc'   => 'Full MVC CRUD scaffold with user management',
    ];

    public function handle(string $type, array $options): void
    {
        $type = strtolower($type);

        if (!isset($this->templates[$type])) {
            echo "Usage: php zero new {type} [--name=...] [--dir=...]\n\n";
            echo "Available project types:\n";
            foreach ($this->templates as $key => $desc) {
                echo sprintf("  %-8s %s\n", $key, $desc);
            }
            echo "\n";
            return;
        }

        $projectName = $this->getOption($options, 'name') ?? 'My App';
        $targetDir = $this->getOption($options, 'dir') ?? getcwd() . '/' . $type . '-app';

        $this->scaffold($type, $targetDir, $projectName);
    }

    private function scaffold(string $type, string $targetDir, string $projectName): void
    {
        $baseDir = dirname(__DIR__, 2) . '/Core/Console/Templates';
        $sourceDir = "$baseDir/$type";
        $sharedDir = "$baseDir/.base";

        if (!is_dir($sourceDir)) {
            echo "Error: Template '$type' not found.\n";
            return;
        }

        if (is_dir($targetDir)) {
            echo "Error: Target directory already exists: $targetDir\n";
            return;
        }

        echo "Creating $type project in $targetDir...\n";

        $this->copyDirectory($sharedDir, $targetDir, $projectName, $type);
        $this->copyDirectory($sourceDir, $targetDir, $projectName, $type);

        $this->removePlaceholder($targetDir);

        echo "Done! Project created at: $targetDir\n";
        echo "\nNext steps:\n";
        echo "  1. cd $targetDir\n";
        echo "  2. Run your web server\n";
        echo "  3. Configure .env\n";
        echo "\n";
    }

    private function copyDirectory(string $source, string $dest, string $projectName, string $type): void
    {
        if (!is_dir($source)) {
            return;
        }

        $items = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($items as $item) {
            $relative = $items->getSubPathname();
            $target = $dest . '/' . $relative;

            if ($item->isDir()) {
                if (!is_dir($target)) {
                    mkdir($target, 0755, true);
                }
            } else {
                $content = file_get_contents($item->getRealPath());
                if ($content === false) {
                    continue;
                }
                if (!is_dir(dirname($target))) {
                    mkdir(dirname($target), 0755, true);
                }
                $content = str_replace(
                    ['{{ project_name }}', '{{ project_type }}', '{{ project_slug }}', '{{ vendor }}', '{{ project_description }}'],
                    [$projectName, strtoupper($type), $this->slugify($projectName), 'vendor', "A $type project"],
                    $content
                );
                file_put_contents($target, $content);
            }
        }
    }

    private function removePlaceholder(string $dir): void
    {
        $gitkeep = $dir . '/.gitkeep';
        if (file_exists($gitkeep)) {
            unlink($gitkeep);
        }
    }

    private function getOption(array $options, string $key): ?string
    {
        foreach ($options as $opt) {
            if (str_starts_with($opt, "--$key=")) {
                return substr($opt, strlen("--$key="));
            }
        }
        return null;
    }

    private function slugify(string $name): string
    {
        return strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $name), '-'));
    }
}
