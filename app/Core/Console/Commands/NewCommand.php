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

    private array $descriptions = [
        'empty' => 'A minimal ZeroPing project skeleton with a welcome page',
        'blog'  => 'A blog application built with ZeroPing, featuring posts and categories',
        'api'   => 'A RESTful API boilerplate with authentication endpoints built with ZeroPing',
        'mvc'   => 'A full MVC CRUD application with user management built with ZeroPing',
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
        $targetDir = $this->getOption($options, 'dir') ?? getcwd() . '/' . $this->slugify($projectName);

        $this->scaffold($type, $targetDir, $projectName);
    }

    private function scaffold(string $type, string $targetDir, string $projectName): void
    {
        $baseDir = dirname(__DIR__, 1) . '/Templates';
        $sourceDir = "$baseDir/$type";
        $sharedDir = "$baseDir/.base";
        $frameworkDir = getcwd();

        if (!is_dir($sourceDir)) {
            (new \App\Core\Console\ConsoleStyle())->writeln("<fg=red>Error:</> Template '<fg=white>$type</>' not found.");
            return;
        }

        if (is_dir($targetDir)) {
            (new \App\Core\Console\ConsoleStyle())->writeln("<fg=red>Error:</> Target directory already exists: <fg=white>$targetDir</>");
            return;
        }

        (new \App\Core\Console\ConsoleStyle())->writeln("<fg=green>Creating <fg=white>$type</> project in <fg=white>$targetDir</>...</>");

        $this->copyDirectory($sharedDir, $targetDir, $projectName, $type);
        $this->copyDirectory($sourceDir, $targetDir, $projectName, $type);

        $this->removePlaceholder($targetDir);
        $this->addFrameworkRepository($targetDir, $frameworkDir);

        $style = new \App\Core\Console\ConsoleStyle();

        $style->writeln("");
        $style->writeln("<fg=green>✔ Done!</> Project created at <fg=cyan>$targetDir</>");
        $style->writeln("");
        $style->writeln("<fg=yellow>▸ Quick start:</>");
        $style->writeln("");
        $style->writeln("  <fg=green>\$</> <fg=white>cd</> <fg=cyan>$targetDir</>");
        $style->writeln("  <fg=green>\$</> <fg=white>composer install</>");
        $style->writeln("  <fg=green>\$</> <fg=white>cp .env.example .env</>");
        $style->writeln("  <fg=green>\$</> <fg=white>php zero key:generate</>");
        $style->writeln("  <fg=green>\$</> <fg=white>php zero serve</>");
        $style->writeln("");
        $style->writeln("  <fg=gray>Then open http://localhost:1437 in your browser</>");
        $style->writeln("");
    }

    private function addFrameworkRepository(string $projectDir, string $frameworkDir): void
    {
        $composerFile = $projectDir . '/composer.json';
        if (!file_exists($composerFile)) {
            return;
        }

        $json = json_decode(file_get_contents($composerFile), true);
        if ($json === null) {
            return;
        }

        $relativePath = $this->getRelativePath($projectDir, $frameworkDir);

        $json['repositories'] = [
            [
                'type' => 'path',
                'url' => $relativePath,
            ],
        ];

        file_put_contents($composerFile, json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n");
    }

    private function getRelativePath(string $from, string $to): string
    {
        $from = str_replace('\\', '/', $from);
        $to = str_replace('\\', '/', $to);
        $fromParts = explode('/', rtrim($from, '/'));
        $toParts = explode('/', rtrim($to, '/'));

        $i = 0;
        while ($i < count($fromParts) && $i < count($toParts) && $fromParts[$i] === $toParts[$i]) {
            $i++;
        }

        $upCount = count($fromParts) - $i;
        $relative = str_repeat('../', $upCount) . implode('/', array_slice($toParts, $i));

        return rtrim($relative, '/');
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
                    ['{{ project_name }}', '{{ project_type }}', '{{ project_slug }}', '{{ vendor }}', '{{ project_description }}', '{{ php_version }}'],
                    [$projectName, strtoupper($type), $this->slugify($projectName), 'vendor', $this->descriptions[$type], PHP_VERSION],
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
