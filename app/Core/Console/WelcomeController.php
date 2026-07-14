<?php

namespace App\Core\Console;

use App\Core\Application\App;
use App\Core\View\Controller;

class WelcomeController extends Controller
{
    public function index(): string
    {
        $name = $this->env('APP_NAME', 'ZeroPing');
        $version = App::VERSION;

        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{$name}</title>
    <style>
        * { box-sizing:border-box; }
        body { margin:0; min-height:100vh; display:flex; align-items:center; justify-content:center;
               font-family:ui-sans-serif,system-ui,-apple-system,Segoe UI,Roboto,sans-serif;
               background:#0b1020; color:#e2e8f0; text-align:center; padding:24px; }
        .logo svg { width:64px; height:64px; }
        h1 { font-size:36px; margin:16px 0 8px; letter-spacing:-.03em;
             background:linear-gradient(135deg,#7c3aed,#22d3ee); -webkit-background-clip:text;
             background-clip:text; color:transparent; }
        p { color:#9fb0d0; margin:0 0 4px; font-size:15px; line-height:1.6; }
        .version { color:#5b6b8c; font-size:13px; margin-top:20px; }
        a { color:#22d3ee; text-decoration:none; }
        a:hover { text-decoration:underline; }
    </style>
</head>
<body>
    <div>
        <div class="logo">
            <svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <defs><linearGradient id="g" x1="0" y1="0" x2="48" y2="48" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#7c3aed"/><stop offset="1" stop-color="#22d3ee"/>
                </linearGradient></defs>
                <circle cx="24" cy="24" r="22" stroke="url(#g)" stroke-width="3"/>
                <path d="M14 24h20M24 14v20" stroke="url(#g)" stroke-width="3" stroke-linecap="round"/>
                <circle cx="24" cy="24" r="5" fill="url(#g)"/>
            </svg>
        </div>
        <h1>{$name}</h1>
        <p>Lightweight PHP Framework</p>
        <p>Fast &bull; Elegant &bull; Extensible</p>
        <p class="version">ZeroPing v{$version}</p>
    </div>
</body>
</html>
HTML;
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
