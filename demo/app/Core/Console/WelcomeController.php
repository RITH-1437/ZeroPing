<?php

namespace App\Core\Console;

use App\Core\Application\App;
use App\Core\View\Controller;

/**
 * Default landing page for a freshly generated ZeroPing application.
 *
 * This is deliberately NOT the framework website — it is a minimal,
 * developer-focused welcome screen showing key runtime info and links to the
 * official documentation, getting-started guide and GitHub repository. It
 * contains no framework marketing.
 */
class WelcomeController extends Controller
{
    public function index(): string
    {
        $name      = config('app.name', 'ZeroPing App');
        $name      = $name === 'ZeroPing' ? 'ZeroPing App' : $name;
        $version   = config('app.starter.version', App::VERSION);
        $php       = PHP_VERSION;
        $env       = config('app.env', 'local');
        $docsUrl   = config('app.docs_url', 'https://zero-ping.duckdns.org');

        return $this->renderWelcome($name, $version, $php, $env, $docsUrl);
    }

    private function renderWelcome(
        string $name,
        string $version,
        string $php,
        string $env,
        string $docsUrl
    ): string {
        $html  = $this->docType($name);
        $html .= $this->styles();
        $html .= "</head>\n<body>\n";
        $html .= "<main class=\"zp-wrap\">\n";

        // Logo
        $html .= "<div class=\"zp-logo\" aria-hidden=\"true\">" . $this->logoSvg() . "</div>\n";

        // Project name + tagline
        $html .= "<h1 class=\"zp-title\">" . htmlspecialchars($name, ENT_QUOTES) . "</h1>\n";
        $html .= "<p class=\"zp-desc\">Your ZeroPing application was created successfully.</p>\n";

        // Runtime info cards
        $html .= "<section class=\"zp-stats\" aria-label=\"Runtime information\">\n";
        $html .= $this->stat('Framework', 'ZeroPing v' . htmlspecialchars($version, ENT_QUOTES));
        $html .= $this->stat('PHP', htmlspecialchars($php, ENT_QUOTES));
        $html .= $this->stat('Environment', htmlspecialchars(ucfirst($env), ENT_QUOTES));
        $html .= "</section>\n";

        // Primary action buttons
        $html .= "<nav class=\"zp-actions\" aria-label=\"Getting started\">\n";
        $html .= $this->button('Documentation', 'https://zero-ping.duckdns.org/docs/introduction');
        $html .= $this->button('Getting Started', 'https://zero-ping.duckdns.org/getting-started');
        $html .= $this->button('GitHub', 'https://github.com/RITH-1437/ZeroPing');
        $html .= "</nav>\n";

        // Secondary doc links
        $html .= "<div class=\"zp-links\">\n";
        foreach ([
            'Features'      => 'https://zero-ping.duckdns.org/features',
            'Installation'  => 'https://zero-ping.duckdns.org/installation',
            'API Reference' => 'https://zero-ping.duckdns.org/api',
        ] as $label => $url) {
            $html .= "<a href=\"" . htmlspecialchars($url, ENT_QUOTES) . "\" rel=\"noopener\">" . htmlspecialchars($label, ENT_QUOTES) . "</a>\n";
        }
        $html .= "</div>\n";

        $html .= "</main>\n</body>\n</html>\n";

        return $html;
    }

    private function stat(string $label, string $value): string
    {
        return "<div class=\"zp-stat\"><span class=\"zp-stat-label\">" . htmlspecialchars($label, ENT_QUOTES)
            . "</span><span class=\"zp-stat-value\">" . $value . "</span></div>\n";
    }

    private function button(string $label, string $url): string
    {
        return "<a class=\"zp-btn\" href=\"" . htmlspecialchars($url, ENT_QUOTES) . "\" rel=\"noopener\">"
            . htmlspecialchars($label, ENT_QUOTES) . "</a>\n";
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
  --card: #0e1626;
  --border: #1c2740;
  --text: #e8eef9;
  --muted: #93a1bd;
  --primary: #22c55e;
  --primary-2: #16a34a;
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
}
.zp-wrap { width: 100%; max-width: 560px; margin: 0 auto; text-align: center; }
.zp-logo { margin: 0 auto 26px; }
.zp-logo svg { width: 64px; height: 64px; filter: drop-shadow(0 0 12px rgba(34,197,94,.45)); }
.zp-title { font-size: 30px; font-weight: 800; letter-spacing: -.025em; margin: 0 0 8px; color: #f4f7fc; }
.zp-desc { color: var(--muted); font-size: 15px; margin: 0 0 28px; }

.zp-stats {
  display: flex; flex-wrap: wrap; justify-content: center; gap: 12px;
  margin: 0 0 30px;
}
.zp-stat {
  background: var(--card); border: 1px solid var(--border); border-radius: 12px;
  padding: 12px 18px; min-width: 120px;
}
.zp-stat-label { display: block; font-size: 11px; letter-spacing: .12em; text-transform: uppercase; color: var(--muted); }
.zp-stat-value { display: block; margin-top: 4px; font-size: 14px; font-weight: 700; color: #d1fae5; }

.zp-actions { display: flex; flex-wrap: wrap; gap: 12px; justify-content: center; margin-bottom: 26px; }
.zp-btn {
  flex: 1 1 140px; text-decoration: none; font-size: 14px; font-weight: 700;
  padding: 13px 18px; border-radius: 12px; color: #04130a;
  background: linear-gradient(135deg, var(--primary), var(--primary-2));
  box-shadow: 0 14px 30px -14px rgba(34,197,94,.7);
  transition: transform .15s, box-shadow .15s;
}
.zp-btn:hover { transform: translateY(-2px); box-shadow: 0 18px 36px -14px rgba(34,197,94,.8); }

.zp-links { display: flex; flex-wrap: wrap; gap: 8px 20px; justify-content: center; }
.zp-links a { color: var(--muted); text-decoration: none; font-size: 13px; }
.zp-links a:hover { color: var(--primary); }
</style>";
    }

    private function logoSvg(): string
    {
        return <<<SVG
<svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
  <defs>
    <linearGradient id="zp" x1="0" y1="0" x2="48" y2="48">
      <stop stop-color="#22c55e"/><stop offset="1" stop-color="#16a34a"/>
    </linearGradient>
  </defs>
  <circle cx="24" cy="24" r="22" stroke="url(#zp)" stroke-width="3"/>
  <path d="M14 24h20M24 14v20" stroke="url(#zp)" stroke-width="3" stroke-linecap="round"/>
  <circle cx="24" cy="24" r="5" fill="url(#zp)"/>
</svg>
SVG;
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
        <p><a href="https://zero-ping.duckdns.org/docs/introduction">Documentation</a> · <a href="./">← back</a></p>
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
        <p><a href="https://zero-ping.duckdns.org/getting-started">Getting Started</a> · <a href="./">← back</a></p>
    </div>
</body>
</html>
HTML;

        return $html;
    }
}
