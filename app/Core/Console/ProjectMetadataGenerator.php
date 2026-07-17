<?php

namespace App\Core\Console;

class ProjectMetadataGenerator
{
    private const STARTER_LABELS = [
        'empty'     => 'Empty Starter',
        'mvc'       => 'MVC Starter',
        'blog'      => 'Blog Starter',
        'api'       => 'REST API Starter',
        'dashboard' => 'Admin Dashboard',
    ];

    private const STARTER_DESCRIPTIONS = [
        'empty'     => 'A lightweight application with only the essentials.',
        'mvc'       => 'A complete MVC application with routing, migrations and authentication.',
        'blog'      => 'Everything required to build a blog.',
        'api'       => 'REST API with authentication and JSON responses.',
        'dashboard' => 'An admin dashboard with stats, tables and user management.',
    ];

    private const EXTRA_ENV_KEYS = [
        'PROJECT_SLUG', 'STARTER_TYPE', 'STARTER_LABEL',
        'STARTER_DESCRIPTION', 'FRAMEWORK_VERSION', 'DOCS_URL',
        'DATABASE_DRIVER',
    ];

    public function __construct(
        private string $projectName,
        private string $starterType,
        private string $frameworkVersion,
        private string $databaseDriver = 'sqlite',
    ) {
    }

    public function slug(): string
    {
        return strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $this->projectName), '-'));
    }

    public function starterLabel(): string
    {
        return self::STARTER_LABELS[$this->starterType] ?? self::STARTER_LABELS['empty'];
    }

    public function starterDescription(): string
    {
        return self::STARTER_DESCRIPTIONS[$this->starterType] ?? self::STARTER_DESCRIPTIONS['empty'];
    }

    public function currentYear(): string
    {
        return date('Y');
    }

    public function phpVersion(): string
    {
        $v = PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION;
        return $v . '+';
    }

    public function databaseDriver(): string
    {
        return strtoupper($this->databaseDriver);
    }

    public function replacements(): array
    {
        return [
            '{{PROJECT_NAME}}'        => $this->projectName,
            '{{ project_name }}'      => $this->projectName,
            '{{PROJECT_SLUG}}'        => $this->slug(),
            '{{ project_slug }}'      => $this->slug(),
            '{{STARTER_TYPE}}'        => $this->starterType,
            '{{ project_type }}'      => $this->starterType,
            '{{STARTER_LABEL}}'       => $this->starterLabel(),
            '{{STARTER_NAME}}'        => $this->starterLabel(),
            '{{STARTER_DESCRIPTION}}' => $this->starterDescription(),
            '{{ project_description }}' => $this->starterDescription(),
            '{{FRAMEWORK_VERSION}}'   => $this->frameworkVersion,
            '{{ php_version }}'       => $this->phpVersion(),
            '{{PHP_VERSION}}'         => $this->phpVersion(),
            '{{CURRENT_YEAR}}'        => $this->currentYear(),
            '{{DATABASE_DRIVER}}'     => $this->databaseDriver(),
            '{{PROJECT_PATH}}'        => $this->slug() . '/',
            '{{ vendor }}'            => 'zeroping',
        ];
    }

    public function replaceInFile(string $filePath): void
    {
        if (!file_exists($filePath)) {
            return;
        }
        $content = file_get_contents($filePath);
        if ($content === false) {
            return;
        }
        $content = str_replace(array_keys($this->replacements()), array_values($this->replacements()), $content);
        file_put_contents($filePath, $content);
    }

    public function replaceInDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }
        $items = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );
        foreach ($items as $item) {
            if ($item->isFile()) {
                $ext = strtolower($item->getExtension());
                if (in_array($ext, ['php', 'html', 'json', 'md', 'env', 'yml', 'yaml', 'xml', 'js', 'css', 'svg', 'env.example'], true)) {
                    $this->replaceInFile($item->getRealPath());
                }
            }
        }
    }

    public function brandEnv(string $envPath): void
    {
        if (!file_exists($envPath)) {
            return;
        }
        $env = file_get_contents($envPath);
        if ($env === false) {
            return;
        }
        $appName = preg_match('/\s/', $this->projectName) ? '"' . $this->projectName . '"' : $this->projectName;

        $env = $this->setEnvVar($env, 'APP_NAME', $appName);
        $env = $this->setEnvVar($env, 'PROJECT_SLUG', $this->slug());
        $env = $this->setEnvVar($env, 'STARTER_TYPE', $this->starterType);
        $env = $this->setEnvVar($env, 'STARTER_LABEL', $this->starterLabel());
        $env = $this->setEnvVar($env, 'STARTER_DESCRIPTION', $this->starterDescription());
        $env = $this->setEnvVar($env, 'FRAMEWORK_VERSION', $this->frameworkVersion);
        $env = $this->setEnvVar($env, 'DATABASE_DRIVER', $this->databaseDriver());

        file_put_contents($envPath, $env);
    }

    public function brandComposerJson(string $composerPath): void
    {
        if (!file_exists($composerPath)) {
            return;
        }
        $raw = (string) file_get_contents($composerPath);
        $json = json_decode($raw, true);
        if ($json === null) {
            return;
        }
        $json['name'] = 'zeroping/' . $this->slug();
        $json['description'] = $this->projectName . ' — ' . $this->starterDescription();
        unset($json['repositories']);
        if (isset($json['config']['allow-plugins']) && $json['config']['allow-plugins'] === []) {
            $json['config']['allow-plugins'] = new \stdClass();
        }
        $flags = JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES;
        if (defined('JSON_UNESCAPED_UNICODE')) {
            $flags |= JSON_UNESCAPED_UNICODE;
        }
        file_put_contents($composerPath, json_encode($json, $flags) . "\n");
    }

    public function generateReadme(string $readmePath): void
    {
        $name = $this->projectName;
        $slug = $this->slug();
        $label = $this->starterLabel();
        $desc = $this->starterDescription();
        $ver = $this->frameworkVersion;
        $year = $this->currentYear();

        $readme = <<<MD
# {$name}

{$desc}

## Project Information

- **Starter Type:** {$label}
- **Framework:** ZeroPing v{$ver}
- **PHP:** {$this->phpVersion()}

## Installation

```bash
composer install
cp .env.example .env
php zero key:generate
```

## Development

```bash
php zero serve
```

Open http://localhost:1437 in your browser.

## Database

```bash
php zero migrate
php zero db:seed
```

## Testing

```bash
php zero test
```

## Project Structure

```
├── app/
│   ├── Controllers/
│   ├── Models/
│   └── Providers/
├── config/
├── database/
│   └── migrations/
├── views/
├── routes/
├── public/
└── storage/
```

## Documentation

- [ZeroPing Documentation](https://github.com/RITH-1437/ZeroPing/tree/main/docs)
- [GitHub Repository](https://github.com/RITH-1437/ZeroPing)
- [Issue Tracker](https://github.com/RITH-1437/ZeroPing/issues)

---

*Generated with ZeroPing Framework v{$ver} — {$year}*
MD;

        file_put_contents($readmePath, $readme);
    }

    public function generateAssets(string $publicDir): void
    {
        $assetsDir = $publicDir . '/assets/images';
        if (!is_dir($assetsDir)) {
            mkdir($assetsDir, 0755, true);
        }

        $primary = '#7c3aed';
        $secondary = '#22d3ee';
        $bg = '#0b1020';

        $favicon = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
  <defs>
    <linearGradient id="g" x1="0" y1="0" x2="32" y2="32">
      <stop stop-color="{$primary}"/><stop offset="1" stop-color="{$secondary}"/>
    </linearGradient>
  </defs>
  <circle cx="16" cy="16" r="14" fill="none" stroke="url(#g)" stroke-width="2"/>
  <path d="M9 16h14M16 9v14" stroke="url(#g)" stroke-width="2" stroke-linecap="round"/>
  <circle cx="16" cy="16" r="3" fill="url(#g)"/>
</svg>
SVG;

        $logo = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 60" fill="none">
  <defs>
    <linearGradient id="g" x1="0" y1="0" x2="80" y2="80">
      <stop stop-color="{$primary}"/><stop offset="1" stop-color="{$secondary}"/>
    </linearGradient>
  </defs>
  <circle cx="30" cy="30" r="22" stroke="url(#g)" stroke-width="3"/>
  <path d="M18 30h24M30 18v24" stroke="url(#g)" stroke-width="3" stroke-linecap="round"/>
  <circle cx="30" cy="30" r="5" fill="url(#g)"/>
  <text x="62" y="36" font-family="system-ui,sans-serif" font-size="22" font-weight="700" fill="#e2e8f0">{$this->projectName}</text>
</svg>
SVG;

        $logoMark = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" fill="none">
  <defs>
    <linearGradient id="g" x1="0" y1="0" x2="48" y2="48">
      <stop stop-color="{$primary}"/><stop offset="1" stop-color="{$secondary}"/>
    </linearGradient>
  </defs>
  <circle cx="24" cy="24" r="22" stroke="url(#g)" stroke-width="3"/>
  <path d="M14 24h20M24 14v20" stroke="url(#g)" stroke-width="3" stroke-linecap="round"/>
  <circle cx="24" cy="24" r="5" fill="url(#g)"/>
</svg>
SVG;

        file_put_contents($assetsDir . '/favicon.svg', $favicon);
        file_put_contents($assetsDir . '/logo.svg', $logo);
        file_put_contents($assetsDir . '/logo-mark.svg', $logoMark);
    }

    private function setEnvVar(string $env, string $key, string $value): string
    {
        if (preg_match('/^' . $key . '=/m', $env)) {
            return preg_replace('/^' . $key . '=.*$/m', $key . '=' . $value, $env);
        }
        return $env . "\n{$key}={$value}";
    }
}
