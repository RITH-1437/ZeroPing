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
        $docsUrl = config('app.docs_url', 'https://zeroping.dev');

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
        $steps = $this->nextSteps();

        $html  = $this->docType($name);
        $html .= $this->styles();
        $html .= "</head>\n<body>\n";
        $html .= "<main class=\"zp-wrap\">\n";

        // ASCII logo
        $html .= "<pre class=\"zp-logo\" aria-hidden=\"true\">" . htmlspecialchars($ascii, ENT_QUOTES) . "</pre>\n";

        // Project name + starter badge
        $html .= "<h1 class=\"zp-title\">" . htmlspecialchars($name, ENT_QUOTES) . "</h1>\n";
        $html .= "<div class=\"zp-badge\">" . htmlspecialchars($starterLabel, ENT_QUOTES) . "</div>\n";

        // Description
        $html .= "<p class=\"zp-desc\">Your ZeroPing application has been created successfully.</p>\n";
        $html .= "<p class=\"zp-starter\">Starter: <strong>" . htmlspecialchars($starterLabel, ENT_QUOTES) . "</strong></p>\n";

        // Terminal card
        $html .= "<section class=\"zp-terminal\" aria-label=\"Quick commands\">\n";
        $html .= "<div class=\"zp-terminal-bar\"><span></span><span></span><span></span></div>\n";
        $html .= "<div class=\"zp-terminal-body\">\n";
        foreach ($commands as $line) {
            $html .= $this->highlightCommand($line) . "\n";
        }
        $html .= "</div>\n</section>\n";

        // Next steps
        $html .= "<section class=\"zp-steps\" aria-label=\"Next steps\">\n";
        foreach ($steps as $step) {
            $html .= "<a class=\"zp-step\" href=\"" . htmlspecialchars($step['href'], ENT_QUOTES) . "\" target=\"_blank\" rel=\"noopener\">\n";
            $html .= "<span class=\"zp-step-title\">" . htmlspecialchars($step['title'], ENT_QUOTES) . "</span>\n";
            $html .= "<span class=\"zp-step-sub\">" . htmlspecialchars($step['sub'], ENT_QUOTES) . "</span>\n";
            $html .= "<span class=\"zp-step-arrow\">&rarr;</span>\n";
            $html .= "</a>\n";
        }
        $html .= "</section>\n";

        // Documentation button
        $html .= "<a class=\"zp-docs\" href=\"" . htmlspecialchars($docsUrl, ENT_QUOTES) . "\" target=\"_blank\" rel=\"noopener\">\n";
        $html .= "Read ZeroPing Documentation &rarr;\n";
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
  --bg: #0a0e1a;
  --bg-soft: #0f1525;
  --card: #111827;
  --border: #1f2937;
  --text: #e6edf7;
  --muted: #8a98b5;
  --faint: #5b6883;
  --primary: #7c3aed;
  --primary-2: #22d3ee;
  --term-bg: #060912;
  --term-text: #cdd6e8;
  --cmd: #22d3ee;
  --flag: #f0a868;
  --comment: #5b6883;
}
* { box-sizing: border-box; }
html, body { margin: 0; padding: 0; }
body {
  min-height: 100vh;
  display: flex; align-items: center; justify-content: center;
  font-family: ui-sans-serif, system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
  background:
    radial-gradient(1200px 600px at 50% -10%, rgba(124,58,237,.18), transparent 60%),
    radial-gradient(900px 500px at 50% 110%, rgba(34,211,238,.12), transparent 60%),
    var(--bg);
  color: var(--text);
  padding: 32px 20px;
  -webkit-font-smoothing: antialiased;
}
.zp-wrap { width: 100%; max-width: 620px; text-align: center; }
.zp-logo {
  font-family: ui-monospace, 'SFMono-Regular', Menlo, Consolas, monospace;
  font-size: 11px; line-height: 1.1; color: transparent;
  background: linear-gradient(135deg, var(--primary), var(--primary-2));
  -webkit-background-clip: text; background-clip: text;
  display: inline-block; margin: 0 0 22px; letter-spacing: 0; font-weight: 700;
  text-shadow: 0 0 24px rgba(124,58,237,.25);
}
.zp-title {
  font-size: 34px; font-weight: 800; letter-spacing: -.03em; margin: 0 0 14px;
  background: linear-gradient(135deg, #fff, #c7d2fe);
  -webkit-background-clip: text; background-clip: text; color: transparent;
}
.zp-badge {
  display: inline-block; padding: 5px 14px; border-radius: 999px;
  font-size: 11px; font-weight: 800; letter-spacing: .14em; text-transform: uppercase;
  color: #fff; background: linear-gradient(135deg, var(--primary), var(--primary-2));
  box-shadow: 0 8px 24px -8px rgba(124,58,237,.6);
}
.zp-desc { color: var(--muted); font-size: 15px; margin: 22px 0 6px; }
.zp-starter { color: var(--faint); font-size: 13px; margin: 0 0 26px; }
.zp-starter strong { color: var(--text); font-weight: 600; }

.zp-terminal {
  text-align: left; background: var(--term-bg); border: 1px solid var(--border);
  border-radius: 16px; overflow: hidden; box-shadow: 0 24px 60px -24px rgba(0,0,0,.7);
}
.zp-terminal-bar { display: flex; gap: 7px; padding: 13px 16px; background: #0b1120; border-bottom: 1px solid var(--border); }
.zp-terminal-bar span { width: 11px; height: 11px; border-radius: 50%; background: #2a3550; }
.zp-terminal-bar span:nth-child(1) { background: #ff5f57; }
.zp-terminal-bar span:nth-child(2) { background: #febc2e; }
.zp-terminal-bar span:nth-child(3) { background: #28c840; }
.zp-terminal-body { padding: 18px 20px; font-family: ui-monospace, 'SFMono-Regular', Menlo, Consolas, monospace; font-size: 13.5px; line-height: 1.9; color: var(--term-text); }
.zp-cmd-line { white-space: pre-wrap; }
.zp-prompt { color: var(--comment); user-select: none; }
.zp-cmd { color: var(--cmd); font-weight: 600; }
.zp-arg { color: #cdd6e8; }
.zp-flag { color: var(--flag); }

.zp-steps { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin: 26px 0; text-align: left; }
.zp-step {
  position: relative; display: block; padding: 16px 18px; border-radius: 14px;
  background: var(--card); border: 1px solid var(--border); text-decoration: none;
  color: var(--text); transition: border-color .18s, transform .18s, background .18s;
}
.zp-step:hover { border-color: rgba(124,58,237,.6); transform: translateY(-2px); background: var(--bg-soft); }
.zp-step-title { display: block; font-size: 14px; font-weight: 700; }
.zp-step-sub { display: block; font-size: 12px; color: var(--faint); margin-top: 3px; }
.zp-step-arrow { position: absolute; top: 18px; right: 18px; color: var(--faint); font-size: 15px; }

.zp-docs {
  display: inline-flex; align-items: center; gap: 8px; margin-top: 6px;
  padding: 14px 26px; border-radius: 12px; font-size: 14px; font-weight: 700;
  text-decoration: none; color: #fff;
  background: linear-gradient(135deg, var(--primary), var(--primary-2));
  box-shadow: 0 14px 34px -12px rgba(124,58,237,.7);
  transition: transform .18s, box-shadow .18s;
}
.zp-docs:hover { transform: translateY(-2px); box-shadow: 0 18px 40px -12px rgba(124,58,237,.85); }

.zp-meta {
  display: flex; flex-wrap: wrap; justify-content: center; gap: 8px 18px;
  margin-top: 30px; padding-top: 20px; border-top: 1px solid var(--border);
  color: var(--faint); font-size: 12px; font-family: ui-monospace, monospace;
}
.zp-meta span { position: relative; }

@media (max-width: 520px) {
  .zp-title { font-size: 28px; }
  .zp-steps { grid-template-columns: 1fr; }
  .zp-logo { font-size: 9px; }
}
</style>";
    }

    /**
     * @return array<int,array{href:string,title:string,sub:string}>
     */
    private function nextSteps(): array
    {
        return [
            [
                'href' => 'https://zeroping.dev/docs/getting-started',
                'title' => 'Create your first controller',
                'sub' => 'php zero make:controller HomeController',
            ],
            [
                'href' => 'https://zeroping.dev',
                'title' => 'Read documentation',
                'sub' => 'Guides, API reference & examples',
            ],
            [
                'href' => 'https://zeroping.dev/docs/database',
                'title' => 'Configure database',
                'sub' => 'Edit .env — DB_CONNECTION, DB_DATABASE',
            ],
            [
                'href' => 'https://zeroping.dev/docs',
                'title' => 'Start development',
                'sub' => 'php zero serve → localhost:1437',
            ],
        ];
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
  ███████╗███████╗██████╗  ██╗███╗   ██╗███████╗███╗   ██╗
  ╚══███╔╝██╔════╝██╔══██╗██╔╝████╗  ██║██╔════╝████╗  ██║
    ███╔╝ █████╗  ██████╔╝██║ ██╔██╗ ██║█████╗  ██╔██╗ ██║
   ███╔╝  ██╔══╝  ██╔══██╗██║ ██║╚██╗██║██╔══╝  ██║╚██╗██║
  ███████╗███████╗██║  ██║██║ ██║ ╚████║███████╗██║ ╚████║
  ╚══════╝╚══════╝╚═╝  ╚═╝╚═╝ ╚═╝  ╚═══╝╚══════╝╚═╝  ╚═══╝
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
