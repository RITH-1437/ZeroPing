<?php

/**
 * Welcome view for a freshly generated ZeroPing application.
 *
 * Rendered by App\Controllers\HomeController::index(). This is a complete,
 * self-contained document (inline CSS, no layout) so generated apps look
 * polished without any build step. Rendered with a `null` layout.
 */

$phpVersion    = PHP_VERSION;
$majorMinor    = PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION;
$appName       = htmlspecialchars($title ?? 'ZeroPing App', ENT_QUOTES);
$starterLabel  = htmlspecialchars($starter ?? 'Starter', ENT_QUOTES);
$zpVersion     = htmlspecialchars($version ?? '2.0.0', ENT_QUOTES);
$env           = htmlspecialchars($environment ?? 'local', ENT_QUOTES);
$dbDriver      = htmlspecialchars($db ?? 'sqlite', ENT_QUOTES);
$cacheDriver   = htmlspecialchars($cache ?? 'file', ENT_QUOTES);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $appName ?></title>
    <style>
        :root {
            --bg: #070b14;
            --card: #0e1626;
            --card-hover: #121e32;
            --border: #1c2740;
            --border-hover: rgba(34,197,94,.25);
            --text: #e8eef9;
            --muted: #7d8fa8;
            --desc: #93a1bd;
            --primary: #22c55e;
            --primary-dark: #16a34a;
            --primary-glow: rgba(34,197,94,.18);
            --cyan: #22d3ee;
            --cyan-glow: rgba(34,211,238,.12);
        }
        *, *::before, *::after { box-sizing: border-box; }
        html, body { margin: 0; padding: 0; }
        body {
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            font-family: ui-sans-serif, system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
            background:
                radial-gradient(900px 500px at 30% 0%, rgba(34,197,94,.08), transparent 60%),
                radial-gradient(700px 400px at 80% 100%, rgba(34,211,238,.06), transparent 60%),
                var(--bg);
            color: var(--text);
            padding: 48px 20px;
            -webkit-font-smoothing: antialiased;
        }
        .wrap { width: 100%; max-width: 620px; margin: 0 auto; }

        /* Logo */
        .logo-wrap { display: flex; justify-content: center; margin-bottom: 28px; }
        .logo-ring {
            width: 72px; height: 72px; border-radius: 50%;
            background: var(--card); border: 1px solid var(--border);
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 0 0 4px var(--primary-glow);
        }
        .logo-ring svg { width: 40px; height: 40px; }

        /* Heading */
        .hero { text-align: center; margin-bottom: 32px; }
        .app-name { font-size: 32px; font-weight: 800; letter-spacing: -.03em; margin: 0 0 6px; }
        .badge {
            display: inline-block; padding: 4px 12px; border-radius: 999px;
            font-size: 11px; font-weight: 700; letter-spacing: .12em; text-transform: uppercase;
            color: #a7f3d0; background: rgba(34,197,94,.10); border: 1px solid rgba(34,197,94,.3);
        }
        .tagline { color: var(--desc); font-size: 15px; margin: 12px auto 0; max-width: 480px; line-height: 1.65; }

        /* Stats row */
        .stats {
            display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px;
            margin: 28px 0;
        }
        @media (max-width: 500px) { .stats { grid-template-columns: repeat(2, 1fr); } }
        .stat {
            background: var(--card); border: 1px solid var(--border); border-radius: 12px;
            padding: 12px 14px; text-align: center;
        }
        .stat-label { display: block; font-size: 10px; letter-spacing: .1em; text-transform: uppercase; color: var(--muted); }
        .stat-value { display: block; margin-top: 4px; font-size: 13px; font-weight: 700; color: #a7f3d0; }

        /* Next steps */
        .section-label {
            font-size: 11px; font-weight: 600; letter-spacing: .1em; text-transform: uppercase;
            color: var(--muted); margin-bottom: 12px;
        }
        .steps { display: flex; flex-direction: column; gap: 8px; margin-bottom: 28px; }
        .step {
            display: flex; align-items: flex-start; gap: 14px;
            background: var(--card); border: 1px solid var(--border); border-radius: 12px;
            padding: 14px 16px; text-decoration: none; color: var(--text);
            transition: background .15s, border-color .15s;
        }
        .step:hover { background: var(--card-hover); border-color: var(--border-hover); }
        .step-num {
            flex-shrink: 0; width: 26px; height: 26px; border-radius: 8px;
            background: var(--primary-glow); border: 1px solid rgba(34,197,94,.2);
            color: var(--primary); font-size: 12px; font-weight: 700;
            display: flex; align-items: center; justify-content: center;
        }
        .step-content { flex: 1; min-width: 0; }
        .step-title { font-size: 14px; font-weight: 600; margin: 0 0 2px; }
        .step-desc { font-size: 13px; color: var(--desc); margin: 0; }
        .step-code {
            display: inline-block; margin-top: 6px; padding: 3px 9px; border-radius: 6px;
            background: rgba(0,0,0,.35); border: 1px solid var(--border);
            font-family: ui-monospace, 'Cascadia Code', Consolas, monospace;
            font-size: 12px; color: var(--primary); letter-spacing: .02em;
        }

        /* Quick links */
        .links { display: flex; flex-wrap: wrap; gap: 8px 0; justify-content: center; }
        .links a {
            padding: 6px 14px; font-size: 13px; color: var(--muted); text-decoration: none;
            border-radius: 8px; transition: color .15s, background .15s;
        }
        .links a:hover { color: var(--primary); background: var(--primary-glow); }

        /* Divider */
        .divider { border: none; border-top: 1px solid var(--border); margin: 24px 0; }
    </style>
</head>
<body>
    <main class="wrap">
        <!-- Logo -->
        <div class="logo-wrap" aria-hidden="true">
            <div class="logo-ring">
                <svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <linearGradient id="zp" x1="0" y1="0" x2="48" y2="48" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#22c55e"/>
                            <stop offset="1" stop-color="#16a34a"/>
                        </linearGradient>
                    </defs>
                    <circle cx="24" cy="24" r="20" stroke="url(#zp)" stroke-width="2.5"/>
                    <path d="M15 24h18M24 15v18" stroke="url(#zp)" stroke-width="2.5" stroke-linecap="round"/>
                    <circle cx="24" cy="24" r="4.5" fill="url(#zp)"/>
                </svg>
            </div>
        </div>

        <!-- Heading -->
        <div class="hero">
            <h1 class="app-name"><?= $appName ?></h1>
            <span class="badge"><?= $starterLabel ?></span>
            <p class="tagline">Your ZeroPing application is ready. A lightweight PHP framework with MVC, ORM, validation, caching, queues, and a full CLI toolkit — all from scratch.</p>
        </div>

        <!-- Runtime stats -->
        <div class="stats" aria-label="Runtime information">
            <div class="stat">
                <span class="stat-label">Framework</span>
                <span class="stat-value">v<?= $zpVersion ?></span>
            </div>
            <div class="stat">
                <span class="stat-label">PHP</span>
                <span class="stat-value"><?= htmlspecialchars($majorMinor, ENT_QUOTES) ?></span>
            </div>
            <div class="stat">
                <span class="stat-label">Environment</span>
                <span class="stat-value"><?= $env ?></span>
            </div>
            <div class="stat">
                <span class="stat-label">Database</span>
                <span class="stat-value"><?= strtoupper($dbDriver) ?></span>
            </div>
        </div>

        <hr class="divider">

        <!-- Next steps -->
        <p class="section-label">Next steps</p>
        <div class="steps">
            <div class="step">
                <span class="step-num">1</span>
                <div class="step-content">
                    <p class="step-title">Define a route</p>
                    <p class="step-desc">Open <code class="step-code">config/routes.php</code> and add your first route.</p>
                </div>
            </div>
            <div class="step">
                <span class="step-num">2</span>
                <div class="step-content">
                    <p class="step-title">Create a controller</p>
                    <p class="step-desc">Generate one from the terminal.</p>
                    <code class="step-code">php zero make:controller UserController</code>
                </div>
            </div>
            <div class="step">
                <span class="step-num">3</span>
                <div class="step-content">
                    <p class="step-title">Set up the database</p>
                    <p class="step-desc">Configure your database in <code class="step-code">.env</code> then run migrations.</p>
                    <code class="step-code">php zero migrate</code>
                </div>
            </div>
            <div class="step">
                <span class="step-num">4</span>
                <div class="step-content">
                    <p class="step-title">Read the documentation</p>
                    <p class="step-desc">Routing, ORM, validation, queues, and CLI reference — all in one place.</p>
                </div>
            </div>
        </div>

        <hr class="divider">

        <!-- Quick links -->
        <nav class="links" aria-label="Resources">
            <a href="https://zero-ping.duckdns.org/docs/introduction">Documentation</a>
            <a href="https://zero-ping.duckdns.org/getting-started">Getting Started</a>
            <a href="https://zero-ping.duckdns.org/docs/cli">CLI Reference</a>
            <a href="https://zero-ping.duckdns.org/docs/database">ORM & Database</a>
            <a href="https://github.com/RITH-1437/ZeroPing">GitHub</a>
        </nav>
    </main>
</body>
</html>
