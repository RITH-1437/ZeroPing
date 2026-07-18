<?php

namespace App\Core\Console;

use App\Core\Application\App;
use App\Core\View\Controller;

/**
 * Default landing page for a freshly generated ZeroPing application.
 *
 * This is deliberately NOT the framework website — it is a minimal,
 * developer-focused welcome screen. It shows that the project was created
 * successfully, which starter template is installed, and how to start
 * building. It contains no framework marketing.
 */
class WelcomeController extends Controller
{
    public function index(): string
    {
        $name = config('app.name', 'ZeroPing App');
        $name = $name === 'ZeroPing' ? 'ZeroPing App' : $name;

        $starterType = strtolower(
            (string) (config('app.starter.type') ?? env('STARTER_TYPE') ?? 'empty')
        );
        $starterType = $this->normalizeStarter($starterType);

        $starterLabel = $this->starterLabel($starterType);

        $version = config('app.starter.version', App::VERSION);
        $php     = PHP_VERSION;
        $env     = config('app.env', 'local');
        $driver  = strtoupper((string) env('DB_CONNECTION', 'sqlite'));
        $docsUrl = config('app.docs_url', 'https://zero-ping.duckdns.org');

        return $this->renderWelcome($name, $starterType, $starterLabel, $version, $php, $env, $driver, $docsUrl);
    }

    /**
     * @param array<string,string> $data
     */
    private function renderWelcome(
        string $name,
        string $starterType,
        string $starterLabel,
        string $version,
        string $php,
        string $env,
        string $driver,
        string $docsUrl
    ): string {
        $ascii = $this->asciiLogo();
        $commands = $this->commandLines();

        $html  = $this->docType($name);
        $html .= $this->styles();
        $html .= "</head>\n<body>\n";
        $html .= "<main class=\"zp-wrap\">\n";

        // ASCII logo
        $html .= "<pre class=\"zp-logo\"><code aria-hidden=\"true\">" . htmlspecialchars($ascii, ENT_QUOTES) . "</code></pre>\n";

        // Project name + starter badge
        $html .= "<h1 class=\"zp-title\">" . htmlspecialchars($name, ENT_QUOTES) . "</h1>\n";
        $html .= "<div class=\"zp-badge\">" . htmlspecialchars($starterLabel, ENT_QUOTES) . "</div>\n";

        // Description
        $html .= "<p class=\"zp-desc\">Your ZeroPing application has been created successfully.</p>\n";

        // Terminal card
        $html .= "<section class=\"zp-terminal\" aria-label=\"Quick commands\">\n";
        $html .= "<div class=\"zp-terminal-bar\"><span></span><span></span><span></span></div>\n";
        $html .= "<div class=\"zp-terminal-body\">\n";
        foreach ($commands as $line) {
            $html .= $this->highlightCommand($line) . "\n";
        }
        $html .= "</div>\n</section>\n";

        // Documentation link
        $html .= "<a class=\"zp-docs-link\" href=\"" . htmlspecialchars($docsUrl, ENT_QUOTES) . "\" rel=\"noopener\">\n";
        $html .= "Read Documentation &rarr;\n";
        $html .= "</a>\n";

        // Footer meta
        $html .= "<footer class=\"zp-meta\">\n";
        $html .= "<span>ZeroPing v" . htmlspecialchars($version, ENT_QUOTES) . "</span>\n";
        $html .= "<span>PHP " . htmlspecialchars($php, ENT_QUOTES) . "</span>\n";
        $html .= "<span>" . htmlspecialchars(ucfirst($env), ENT_QUOTES) . "</span>\n";
        $html .= "<span>" . htmlspecialchars($driver, ENT_QUOTES) . "</span>\n";
        $html .= "</footer>\n";

        $html .= "</main>\n</body>\n</html>\n";

        return $html;
    }

    private function docType(string $name): string
    {
        return "<!DOCTYPE html>\n<html lang=\"en\">\n<head>\n"
            . "<meta charset=\"utf-8\">\n"
            . "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">\n"
            . "<title>" . htmlspecialchars($name, ENT_QUOTES) . " — ZeroPing</title>\n";
    }

    private function styles(): string
    {
        return "<style>
:root {
  --bg: #070b14;
  --bg-soft: #0d1424;
  --card: #0e1626;
  --border: #1c2740;
  --text: #e8eef9;
  --muted: #93a1bd;
  --faint: #5d6b87;
  --primary: #22c55e;
  --primary-2: #16a34a;
  --term-bg: #050912;
  --term-text: #c7d2e8;
  --cmd: #22c55e;
  --flag: #f0a868;
  --comment: #5d6b87;
}
* { box-sizing: border-box; }
html, body { margin: 0; padding: 0; }
body {
  min-height: 100vh;
  display: flex; align-items: center; justify-content: center;
  font-family: ui-sans-serif, system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
  background:
    radial-gradient(1000px 520px at 50% -12%, rgba(34,197,94,.16), transparent 62%),
    radial-gradient(800px 480px at 50% 112%, rgba(22,163,74,.10), transparent 60%),
    var(--bg);
  color: var(--text);
  padding: 40px 20px;
  -webkit-font-smoothing: antialiased;
  text-rendering: optimizeLegibility;
}
.zp-wrap { width: 100%; max-width: 600px; margin: 0 auto; text-align: center; }
.zp-logo {
  font-family: 'JetBrains Mono', 'Fira Code', 'Cascadia Code', Consolas, monospace;
  font-size: clamp(13px, 1.6vw, 18px);
  line-height: 0.9;
  letter-spacing: 0;
  white-space: pre;
  display: inline-block;
  text-rendering: geometricPrecision;
  font-feature-settings: 'liga' 0;
  font-variant-ligatures: none;
  text-align: center;
  margin: 0 auto 30px; padding: 10px 0; color: #22C55E;
  text-shadow: 0 0 8px rgba(34,197,94,.45), 0 0 22px rgba(34,197,94,.18);
  image-rendering: auto; transform: none; font-weight: 700;
}
.zp-logo code { font: inherit; color: inherit; text-shadow: inherit; white-space: pre; }
.zp-title {
  font-size: 32px; font-weight: 800; letter-spacing: -.025em; margin: 0 0 12px;
  color: #f4f7fc;
}
.zp-badge {
  display: inline-block; padding: 5px 14px; border-radius: 999px;
  font-size: 11px; font-weight: 700; letter-spacing: .14em; text-transform: uppercase;
  color: #d1fae5; background: rgba(34,197,94,.10);
  border: 1px solid rgba(34,197,94,.35);
  backdrop-filter: blur(6px);
}
.zp-desc { color: var(--muted); font-size: 15px; margin: 38px 0 0; }

.zp-terminal {
  text-align: left; background: var(--term-bg); border: 1px solid var(--border);
  border-radius: 16px; overflow: hidden;
  box-shadow: 0 30px 80px -30px rgba(0,0,0,.8), 0 0 0 1px rgba(34,197,94,.04) inset;
  margin: 36px auto 0; max-width: 540px;
}
.zp-terminal-bar {
  display: flex; gap: 7px; padding: 13px 16px; background: #0a1020;
  border-bottom: 1px solid var(--border);
}
.zp-terminal-bar span { width: 11px; height: 11px; border-radius: 50%; background: #2a3550; }
.zp-terminal-bar span:nth-child(1) { background: #ff5f57; }
.zp-terminal-bar span:nth-child(2) { background: #febc2e; }
.zp-terminal-bar span:nth-child(3) { background: #28c840; }
.zp-terminal-body { padding: 18px 20px; font-family: 'JetBrains Mono', 'Fira Code', 'Cascadia Code', Consolas, monospace; font-size: 13.5px; line-height: 1.9; color: var(--term-text); }
.zp-cmd-line { white-space: pre-wrap; }
.zp-prompt { color: var(--comment); user-select: none; }
.zp-cmd { color: var(--cmd); font-weight: 600; }
.zp-arg { color: #c7d2e8; }
.zp-flag { color: var(--flag); }

.zp-docs-link {
  display: inline-block; margin-top: 34px; text-decoration: none;
  font-size: 14px; font-weight: 500; color: var(--muted);
  letter-spacing: .01em; transition: color .18s;
}
.zp-docs-link:hover { color: var(--primary); }

.zp-meta {
  display: flex; flex-wrap: wrap; justify-content: center; gap: 8px 18px;
  margin-top: 30px; padding-top: 20px; border-top: 1px solid var(--border);
  color: var(--faint); font-size: 12px; font-family: 'JetBrains Mono', 'Fira Code', Consolas, monospace;
}
.zp-meta span { position: relative; }

@media (max-width: 520px) {
  .zp-title { font-size: 26px; }
  .zp-logo { font-size: 8px; }
}
</style>";
    }

    /**
     * @return array<int,string>
     */
    private function commandLines(): array
    {
        return [
            'php zero serve',
            'php zero route:list',
            'php zero make:controller HomeController',
            'php zero make:model User',
            'php zero migrate',
            'composer install',
        ];
    }

    private function highlightCommand(string $line): string
    {
        $parts = preg_split('/(\s+)/', $line, -1, PREG_SPLIT_DELIM_CAPTURE);
        $out = '<div class="zp-cmd-line"><span class="zp-prompt">$ </span>';
        $first = true;
        foreach ($parts as $p) {
            if ($p === '' || trim($p) === '') {
                $out .= $p;
                continue;
            }
            if ($first) {
                $out .= '<span class="zp-cmd">' . htmlspecialchars($p, ENT_QUOTES) . '</span>';
                $first = false;
            } elseif (str_starts_with($p, '-')) {
                $out .= '<span class="zp-flag">' . htmlspecialchars($p, ENT_QUOTES) . '</span>';
            } else {
                $out .= '<span class="zp-arg">' . htmlspecialchars($p, ENT_QUOTES) . '</span>';
            }
        }
        $out .= '</div>';

        return $out;
    }

    private function asciiLogo(): string
    {
        return <<<LOGO
███████╗███████╗██████╗  ██████╗ ██████╗ ██╗███╗   ██╗ ██████╗
╚══███╔╝██╔════╝██╔══██╗██╔═══██╗██╔══██╗██║████╗  ██║██╔════╝
  ███╔╝ █████╗  ██████╔╝██║   ██║██████╔╝██║██╔██╗ ██║██║  ███╗
 ███╔╝  ██╔══╝  ██╔══██╗██║   ██║██╔═══╝ ██║██║╚██╗██║██║   ██║
███████╗███████╗██║  ██║╚██████╔╝██║     ██║██║ ╚████║╚██████╔╝
╚══════╝╚══════╝╚═╝  ╚═╝ ╚═════╝ ╚═╝     ╚═╝╚═╝  ╚═══╝ ╚═════╝
LOGO;
    }

    private function normalizeStarter(string $type): string
    {
        $map = [
            'empty' => 'empty',
            'mvc' => 'mvc',
            'blog' => 'blog',
            'api' => 'api',
            'dashboard' => 'dashboard',
        ];

        return $map[$type] ?? 'empty';
    }

    private function starterLabel(string $type): string
    {
        return match ($type) {
            'mvc' => 'MVC',
            'blog' => 'Blog',
            'api' => 'API',
            'dashboard' => 'Dashboard',
            default => 'Empty',
        };
    }

    public function cli(): string
    {
        $appName = config('app.name', 'ZeroPing');
        $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CLI Tooling — {$appName}</title>
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
        $appName = config('app.name', 'ZeroPing');
        $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quick Start — {$appName}</title>
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
}
