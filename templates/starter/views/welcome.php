<?php

/**
 * Welcome view for a freshly generated ZeroPing application.
 *
 * Rendered by App\Controllers\HomeController::index(). Demonstrates passing
 * data from a controller into a view. Keep this self-contained (inline CSS)
 * so generated apps look polished without any build step.
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($title ?? 'ZeroPing', ENT_QUOTES) ?></title>
    <style>
        :root {
            --bg: #070b14;
            --text: #e8eef9;
            --muted: #93a1bd;
            --primary: #22c55e;
        }
        * { box-sizing: border-box; }
        body {
            min-height: 100vh; margin: 0; padding: 40px 20px;
            display: flex; align-items: center; justify-content: center;
            font-family: ui-sans-serif, system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
            background: radial-gradient(900px 480px at 50% -12%, rgba(34,197,94,.16), transparent 62%), var(--bg);
            color: var(--text);
        }
        .card { width: 100%; max-width: 560px; text-align: center; }
        h1 { font-size: 30px; font-weight: 800; letter-spacing: -.025em; margin: 0 0 8px; }
        .badge {
            display: inline-block; padding: 5px 14px; border-radius: 999px;
            font-size: 11px; font-weight: 700; letter-spacing: .14em; text-transform: uppercase;
            color: #d1fae5; background: rgba(34,197,94,.10); border: 1px solid rgba(34,197,94,.35);
        }
        p { color: var(--muted); margin: 24px 0; }
        a {
            display: inline-block; margin: 6px; padding: 10px 18px; border-radius: 10px;
            text-decoration: none; font-size: 14px; font-weight: 600;
            color: #d1fae5; background: rgba(34,197,94,.12); border: 1px solid rgba(34,197,94,.35);
        }
        a:hover { background: rgba(34,197,94,.22); }
        .meta { margin-top: 28px; color: var(--muted); font-size: 12px; font-family: ui-monospace, monospace; }
    </style>
</head>
<body>
    <main class="card">
        <h1><?= htmlspecialchars($title ?? 'ZeroPing App', ENT_QUOTES) ?></h1>
        <div class="badge"><?= htmlspecialchars($starter ?? 'Starter', ENT_QUOTES) ?></div>
        <p><?= htmlspecialchars($message ?? 'Your ZeroPing application was created successfully.', ENT_QUOTES) ?></p>
        <a href="https://zero-ping.duckdns.org/docs/introduction">Documentation</a>
        <a href="https://zero-ping.duckdns.org/getting-started">Getting Started</a>
        <a href="https://github.com/RITH-1437/ZeroPing">GitHub</a>
        <div class="meta">ZeroPing v<?= htmlspecialchars($version ?? '2.0.0', ENT_QUOTES) ?> &middot; PHP <?= htmlspecialchars($php ?? PHP_VERSION, ENT_QUOTES) ?></div>
    </main>
</body>
</html>
