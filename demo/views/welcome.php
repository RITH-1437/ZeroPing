<?php

/**
 * Welcome view for a freshly generated ZeroPing application.
 *
 * Rendered by App\Controllers\HomeController::index(). This is a complete,
 * self-contained document (inline CSS, no layout) so generated apps look
 * polished without any build step. Rendered with a `null` layout.
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($title ?? 'ZeroPing App', ENT_QUOTES) ?></title>
    <style>
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
        .wrap { width: 100%; max-width: 560px; margin: 0 auto; text-align: center; }
        .logo { margin: 0 auto 26px; }
        .logo svg { width: 64px; height: 64px; filter: drop-shadow(0 0 12px rgba(34,197,94,.45)); }
        .title { font-size: 30px; font-weight: 800; letter-spacing: -.025em; margin: 0 0 8px; color: #f4f7fc; }
        .badge {
            display: inline-block; padding: 5px 14px; border-radius: 999px;
            font-size: 11px; font-weight: 700; letter-spacing: .14em; text-transform: uppercase;
            color: #d1fae5; background: rgba(34,197,94,.10); border: 1px solid rgba(34,197,94,.35);
        }
        .desc { color: var(--muted); font-size: 15px; margin: 16px auto 28px; max-width: 460px; line-height: 1.6; }
        .stats { display: flex; flex-wrap: wrap; justify-content: center; gap: 12px; margin: 0 0 30px; }
        .stat { background: var(--card); border: 1px solid var(--border); border-radius: 12px; padding: 12px 18px; min-width: 120px; }
        .stat-label { display: block; font-size: 11px; letter-spacing: .12em; text-transform: uppercase; color: var(--muted); }
        .stat-value { display: block; margin-top: 4px; font-size: 14px; font-weight: 700; color: #d1fae5; }
        .actions { display: flex; flex-wrap: wrap; gap: 12px; justify-content: center; margin-bottom: 26px; }
        .btn {
            flex: 1 1 140px; text-decoration: none; font-size: 14px; font-weight: 700;
            padding: 13px 18px; border-radius: 12px; color: #04130a;
            background: linear-gradient(135deg, var(--primary), var(--primary-2));
            box-shadow: 0 14px 30px -14px rgba(34,197,94,.7);
            transition: transform .15s, box-shadow .15s;
        }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 18px 36px -14px rgba(34,197,94,.8); }
        .links { display: flex; flex-wrap: wrap; gap: 8px 20px; justify-content: center; }
        .links a { color: var(--muted); text-decoration: none; font-size: 13px; }
        .links a:hover { color: var(--primary); }
    </style>
</head>
<body>
    <main class="wrap">
        <div class="logo" aria-hidden="true">
            <svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <linearGradient id="zp" x1="0" y1="0" x2="48" y2="48">
                        <stop stop-color="#22c55e"/><stop offset="1" stop-color="#16a34a"/>
                    </linearGradient>
                </defs>
                <circle cx="24" cy="24" r="22" stroke="url(#zp)" stroke-width="3"/>
                <path d="M14 24h20M24 14v20" stroke="url(#zp)" stroke-width="3" stroke-linecap="round"/>
                <circle cx="24" cy="24" r="5" fill="url(#zp)"/>
            </svg>
        </div>

        <h1 class="title"><?= htmlspecialchars($title ?? 'ZeroPing App', ENT_QUOTES) ?></h1>
        <span class="badge"><?= htmlspecialchars($starter ?? 'Starter', ENT_QUOTES) ?></span>
        <p class="desc">
            A lightweight, modern PHP framework with a clean MVC architecture, a multi-driver
            ORM, validation, caching, queues and batteries-included CLI tooling. Your application
            was created successfully and is ready to build on.
        </p>

        <section class="stats" aria-label="Runtime information">
            <div class="stat">
                <span class="stat-label">Framework</span>
                <span class="stat-value">ZeroPing v<?= htmlspecialchars($version ?? '2.0.0', ENT_QUOTES) ?></span>
            </div>
            <div class="stat">
                <span class="stat-label">PHP</span>
                <span class="stat-value"><?= htmlspecialchars($php ?? PHP_VERSION, ENT_QUOTES) ?></span>
            </div>
        </section>

        <nav class="actions" aria-label="Getting started">
            <a class="btn" href="https://zero-ping.duckdns.org/docs/introduction">Documentation</a>
            <a class="btn" href="https://zero-ping.duckdns.org/getting-started">Getting Started</a>
            <a class="btn" href="https://github.com/RITH-1437/ZeroPing">GitHub</a>
        </nav>

        <div class="links">
            <a href="https://zero-ping.duckdns.org/features">Features</a>
            <a href="https://zero-ping.duckdns.org/installation">Installation</a>
            <a href="https://zero-ping.duckdns.org/api">API Reference</a>
        </div>
    </main>
</body>
</html>
