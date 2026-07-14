<?php

namespace App\Core\Console;

use App\Core\Application\App;
use App\Core\View\Controller;

class WelcomeController extends Controller
{
    public function index(): string
    {
        $version = App::VERSION;
        $php = PHP_VERSION;
        $env = $this->env('APP_ENV', 'production');
        $name = $this->env('APP_NAME', 'ZeroPing');
        $driver = strtoupper($this->env('DB_CONNECTION', 'sqlite'));
        $release = $this->recentRelease();

        $dark = <<<'CSS'
            :root { color-scheme: dark; }
            body { background:#0b1020; }
            .card { background:#11182e; border-color:#1f2a44; }
            .sub { color:#9fb0d0; }
            .pill { background:#16213c; border-color:#243154; color:#cdd9f5; }
            .term { background:#05080f; border-color:#1b2740; color:#9fe6b0; }
            .brand { background:linear-gradient(135deg,#7c3aed,#22d3ee); -webkit-background-clip:text; background-clip:text; color:transparent; }
            .accent { color:#22d3ee; }
CSS;

        $light = <<<'CSS'
            :root { color-scheme: light; }
            body { background:#f6f8fc; }
            .card { background:#ffffff; border-color:#e4e9f2; }
            .sub { color:#5b6b8c; }
            .pill { background:#eef2fb; border-color:#dbe3f3; color:#334155; }
            .term { background:#0b1020; border-color:#1b2740; color:#9fe6b0; }
            .brand { background:linear-gradient(135deg,#6d28d9,#0891b2); -webkit-background-clip:text; background-clip:text; color:transparent; }
            .accent { color:#0e7490; }
CSS;

        $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{$name} — ZeroPing Framework</title>
    <style>
        * { box-sizing:border-box; }
        body {
            margin:0; font-family:ui-sans-serif,system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial,sans-serif;
            color:#1f2937; line-height:1.55; transition:background .2s ease,color .2s ease;
        }
        body.dark { $dark }
        body:not(.dark) { $light }
        .wrap { max-width:860px; margin:0 auto; padding:48px 24px 64px; }
        header { display:flex; align-items:center; justify-content:space-between; gap:16px; }
        .logo { display:flex; align-items:center; gap:12px; }
        .logo svg { width:40px; height:40px; }
        h1 { font-size:30px; margin:0; letter-spacing:-.02em; }
        .tag { font-size:14px; margin:4px 0 0; }
        .toggle { cursor:pointer; border:1px solid; border-radius:999px; padding:8px 14px; font-size:13px; background:transparent; }
        .pills { display:flex; flex-wrap:wrap; gap:10px; margin:26px 0 8px; }
        .pill { border:1px solid; border-radius:999px; padding:6px 14px; font-size:13px; font-weight:600; }
        .grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(190px,1fr)); gap:16px; margin-top:28px; }
        .card { border:1px solid; border-radius:16px; padding:20px; text-decoration:none; color:inherit; transition:transform .12s ease, box-shadow .12s ease; }
        .card:hover { transform:translateY(-2px); box-shadow:0 10px 30px rgba(15,23,42,.12); }
        .card h3 { margin:0 0 6px; font-size:16px; }
        .card p { margin:0; font-size:14px; }
        .term { border:1px solid; border-radius:14px; padding:18px 20px; margin-top:30px; font-family:ui-monospace,SFMono-Regular,Menlo,Consolas,monospace; font-size:13.5px; white-space:pre; overflow-x:auto; }
        .term .dim { opacity:.65; }
        footer { margin-top:34px; font-size:13px; text-align:center; }
        a { color:inherit; }
    </style>
    <script>
        (function () {
            try {
                var d = localStorage.getItem('zeroping-theme') === 'dark'
                    || (!localStorage.getItem('zeroping-theme') && matchMedia('(prefers-color-scheme: dark)').matches);
                if (d) document.body.classList.add('dark');
            } catch (e) {}
        })();
        function toggleTheme() {
            var b = document.body.classList.toggle('dark');
            try { localStorage.setItem('zeroping-theme', b ? 'dark' : 'light'); } catch (e) {}
        }
    </script>
</head>
<body>
    <div class="wrap">
        <header>
            <div class="logo">
                <svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <defs><linearGradient id="g" x1="0" y1="0" x2="48" y2="48" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#7c3aed"/><stop offset="1" stop-color="#22d3ee"/>
                    </linearGradient></defs>
                    <circle cx="24" cy="24" r="22" stroke="url(#g)" stroke-width="3"/>
                    <path d="M14 24h20M24 14v20" stroke="url(#g)" stroke-width="3" stroke-linecap="round"/>
                    <circle cx="24" cy="24" r="5" fill="url(#g)"/>
                </svg>
                <div>
                    <h1 class="brand">{$name}</h1>
                    <p class="tag sub">Powered by the ZeroPing Framework</p>
                </div>
            </div>
            <button class="toggle" onclick="toggleTheme()">Toggle theme</button>
        </header>

        <div class="pills">
            <span class="pill">Framework v{$version}</span>
            <span class="pill">PHP {$php}</span>
            <span class="pill">Database: {$driver}</span>
            <span class="pill">Environment: {$env}</span>
            <span class="pill">Latest: {$release}</span>
        </div>

        <div class="grid">
            <a class="card" href="https://github.com/RITH-1437/ZeroPing/tree/main/docs" target="_blank" rel="noopener">
                <h3>Documentation</h3>
                <p class="sub">Guides, routing, ORM, CLI and deployment references.</p>
            </a>
            <a class="card" href="https://github.com/RITH-1437/ZeroPing" target="_blank" rel="noopener">
                <h3>GitHub</h3>
                <p class="sub">Source, issues and release notes.</p>
            </a>
            <a class="card" href="/cli">
                <h3>CLI Tooling</h3>
                <p class="sub">Run <span class="accent">php zero</span> for commands and wizards.</p>
            </a>
            <a class="card" href="/start">
                <h3>Quick Start</h3>
                <p class="sub">Edit routes in <span class="accent">config/routes.php</span> to begin.</p>
            </a>
        </div>

        <div class="term"><span class="dim">$</span> php zero serve
<span class="dim"># Server started on http://localhost:1437</span>

<span class="dim">$</span> php zero doctor <span class="dim"># verify your environment</span>
<span class="dim">$</span> php zero install <span class="dim"># interactive setup wizard</span></div>

        <footer class="sub">ZeroPing Framework v{$version} · Built for developers who value a fast, clean start.</footer>
    </div>
</body>
</html>
HTML;

        return $html;
    }

    public function cli(): string
    {
        $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CLI Tooling — {$this->env('APP_NAME', 'ZeroPing')}</title>
    <style>
        * { box-sizing:border-box; }
        body { margin:0; font-family:ui-monospace,SFMono-Regular,Menlo,Consolas,monospace; background:#05080f; color:#9fe6b0; padding:40px 24px; }
        .wrap { max-width:760px; margin:0 auto; }
        h1 { color:#e6f6ee; font-size:24px; }
        .cmd { background:#0b1020; border:1px solid #1b2740; border-radius:12px; padding:14px 18px; margin:10px 0; }
        .cmd b { color:#22d3ee; }
        a { color:#7dd3fc; }
    </style>
</head>
<body>
    <div class="wrap">
        <h1>ZeroPing CLI</h1>
        <div class="cmd"><b>php zero serve</b> — start the development server</div>
        <div class="cmd"><b>php zero install</b> — interactive setup wizard</div>
        <div class="cmd"><b>php zero doctor</b> — verify your environment</div>
        <div class="cmd"><b>php zero migrate</b> — run database migrations</div>
        <div class="cmd"><b>php zero make:controller</b> — scaffold a controller</div>
        <div class="cmd"><b>php zero make:model</b> — scaffold a model</div>
        <p><a href="./">← back</a></p>
    </div>
</body>
</html>
HTML;

        return $html;
    }

    public function start(): string
    {
        $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quick Start — {$this->env('APP_NAME', 'ZeroPing')}</title>
    <style>
        * { box-sizing:border-box; }
        body { margin:0; font-family:ui-sans-serif,system-ui,-apple-system,Segoe UI,Roboto,sans-serif; background:#f6f8fc; color:#1f2937; padding:40px 24px; }
        .wrap { max-width:760px; margin:0 auto; }
        h1 { font-size:26px; }
        pre { background:#0b1020; color:#9fe6b0; padding:16px 20px; border-radius:12px; overflow-x:auto; }
        a { color:#0e7490; }
    </style>
</head>
<body>
    <div class="wrap">
        <h1>Quick Start</h1>
        <p>Edit your routes in <code>config/routes.php</code> and create controllers under <code>app/Controllers</code>.</p>
        <pre># Start the server
php zero serve

# Create a controller
php zero make:controller PostController

# Run migrations
php zero migrate</pre>
        <p><a href="./">← back</a></p>
    </div>
</body>
</html>
HTML;

        return $html;
    }

    /**
     * Latest GitHub release tag, cached for an hour to avoid API rate limits.
     */
    private function recentRelease(): string
    {
        $base = defined('BASE_PATH') ? rtrim(BASE_PATH, '/') : sys_get_temp_dir();
        $cacheDir = $base . '/bootstrap/cache';
        $cache = $cacheDir . '/release.txt';
        $fallback = 'v' . App::VERSION;

        if (file_exists($cache) && (time() - filemtime($cache)) < 3600) {
            $cached = @file_get_contents($cache);
            if ($cached !== false && $cached !== '') {
                return trim($cached);
            }
        }

        try {
            $ctx = stream_context_create(['http' => ['timeout' => 2, 'header' => "User-Agent: ZeroPing\r\n"]]);
            $json = @file_get_contents('https://api.github.com/repos/RITH-1437/ZeroPing/releases/latest', false, $ctx);
            $tag = $json !== false ? (json_decode($json, true)['tag_name'] ?? null) : null;

            if (!empty($tag)) {
                if (!is_dir($cacheDir)) {
                    @mkdir($cacheDir, 0775, true);
                }
                @file_put_contents($cache, $tag);
                return $tag;
            }
        } catch (\Throwable $e) {
            // fall through to fallback
        }

        return $fallback;
    }

    private function env(string $key, string $default = ''): string
    {
        $value = $_ENV[$key] ?? getenv($key);
        if ($value === false || $value === null) {
            return $default;
        }

        return (string) $value;
    }
}
