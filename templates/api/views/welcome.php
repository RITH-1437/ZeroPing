<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($title ?? 'API', ENT_QUOTES) ?> — ZeroPing</title>
    <style>
        :root {
            --bg: #070b14;
            --card: #0e1626;
            --border: #1c2740;
            --text: #e8eef9;
            --muted: #93a1bd;
            --primary: #22c55e;
            --primary-2: #16a34a;
            --accent: #38bdf8;
        }
        * { box-sizing: border-box; }
        html, body { margin: 0; padding: 0; }
        body {
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            font-family: ui-sans-serif, system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
            background:
                radial-gradient(1000px 520px at 50% -12%, rgba(56,189,248,.10), transparent 62%),
                radial-gradient(800px 480px at 50% 112%, rgba(34,197,94,.06), transparent 60%),
                var(--bg);
            color: var(--text);
            padding: 40px 20px;
            -webkit-font-smoothing: antialiased;
        }
        .wrap { width: 100%; max-width: 640px; margin: 0 auto; }
        .header { text-align: center; margin-bottom: 36px; }
        .logo { margin: 0 auto 24px; }
        .logo svg { width: 52px; height: 52px; filter: drop-shadow(0 0 10px rgba(56,189,248,.35)); }
        .title { font-size: 28px; font-weight: 800; letter-spacing: -.025em; margin: 0 0 4px; color: #f4f7fc; }
        .badge {
            display: inline-block; padding: 4px 12px; border-radius: 999px;
            font-size: 10px; font-weight: 700; letter-spacing: .14em; text-transform: uppercase;
            color: #7dd3fc; background: rgba(56,189,248,.08); border: 1px solid rgba(56,189,248,.25);
        }
        .desc { color: var(--muted); font-size: 14px; margin: 12px 0 0; }

        .step {
            display: flex;
            gap: 16px;
            padding: 18px 20px;
            border-radius: 14px;
            border: 1px solid var(--border);
            background: var(--card);
            margin-bottom: 12px;
            align-items: flex-start;
            transition: border-color .15s;
        }
        .step.active { border-color: rgba(56,189,248,.4); }
        .step.done { border-color: rgba(34,197,94,.3); }
        .step-number {
            width: 32px; height: 32px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 13px; font-weight: 700; flex-shrink: 0;
        }
        .step-number.pending { background: rgba(56,189,248,.12); color: #7dd3fc; }
        .step-number.complete { background: rgba(34,197,94,.12); color: #86efac; }
        .step-number.idle { background: rgba(148,163,184,.08); color: #64748b; }
        .step-body { flex: 1; min-width: 0; }
        .step-title { font-size: 15px; font-weight: 700; color: var(--text); margin-bottom: 4px; }
        .step-desc { font-size: 13px; color: var(--muted); line-height: 1.5; }
        .step-action { margin-top: 10px; }

        .endpoint-group {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 20px;
            margin-top: 20px;
        }
        .endpoint-group-title {
            font-size: 11px; font-weight: 700; letter-spacing: .1em; text-transform: uppercase;
            color: var(--muted); margin-bottom: 12px;
        }
        .endpoint {
            display: flex; align-items: center; gap: 10px;
            padding: 8px 10px; border-radius: 8px;
            transition: background .12s;
        }
        .endpoint:hover { background: rgba(255,255,255,.04); }
        .endpoint + .endpoint { margin-top: 4px; }
        .method {
            display: inline-block; padding: 2px 7px; border-radius: 5px;
            font-size: 10px; font-weight: 700; font-family: ui-monospace, monospace;
            min-width: 44px; text-align: center;
        }
        .method-get { background: rgba(56,189,248,.12); color: #7dd3fc; }
        .method-post { background: rgba(34,197,94,.12); color: #86efac; }
        .path { font-size: 13px; font-weight: 600; color: var(--text); font-family: ui-monospace, monospace; }
        .endpoint-desc { font-size: 12px; color: var(--muted); margin-left: auto; }

        .stats { display: flex; gap: 10px; margin-top: 20px; }
        .stat {
            flex: 1; background: var(--card); border: 1px solid var(--border); border-radius: 10px;
            padding: 12px 14px; text-align: center;
        }
        .stat-label { display: block; font-size: 9px; letter-spacing: .12em; text-transform: uppercase; color: var(--muted); }
        .stat-value { display: block; margin-top: 3px; font-size: 13px; font-weight: 700; color: #d1fae5; }

        .code-inline {
            font-family: ui-monospace, monospace;
            font-size: 12px;
            padding: 5px 10px;
            border-radius: 6px;
            background: rgba(255,255,255,.04);
            border: 1px solid rgba(255,255,255,.06);
            color: #7dd3fc;
            display: inline-block;
        }

        .footer-links { text-align: center; margin-top: 28px; }
        .footer-links a { color: var(--muted); text-decoration: none; font-size: 13px; margin: 0 12px; }
        .footer-links a:hover { color: var(--accent); }
    </style>
</head>
<body>
    <main class="wrap">
        <div class="header">
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
            <h1 class="title"><?= htmlspecialchars($title ?? 'API', ENT_QUOTES) ?></h1>
            <span class="badge">RESTful API</span>
            <p class="desc">A lightweight API skeleton with authentication and resource endpoints.</p>
        </div>

        <div class="step active">
            <div class="step-number pending">1</div>
            <div class="step-body">
                <div class="step-title">Set Up Authentication</div>
                <div class="step-desc">Implement your login logic in <code class="code-inline">app/Controllers/AuthController.php</code>. The <code class="code-inline">POST /api/login</code> endpoint is ready for your token or session logic.</div>
                <div class="step-action">
                    <a href="https://zero-ping.duckdns.org/docs/security" class="code-inline" style="text-decoration:none;color:#93a1bd;border-color:var(--border);">View Security Docs</a>
                </div>
            </div>
        </div>

        <div class="step">
            <div class="step-number idle">2</div>
            <div class="step-body">
                <div class="step-title">Test Your Endpoints</div>
                <div class="step-desc">Use curl, Postman, or any HTTP client to interact with the API. Start with the health check to confirm the server is running.</div>
                <div class="step-action">
                    <code class="code-inline">curl http://localhost:1437/health</code>
                </div>
            </div>
        </div>

        <div class="step">
            <div class="step-number idle">3</div>
            <div class="step-body">
                <div class="step-title">Build Your API</div>
                <div class="step-desc">Extend the scaffold by adding resources, middleware, and business logic. Generate new controllers and models as needed.</div>
                <div class="step-action" style="display:flex;flex-wrap:wrap;gap:6px;">
                    <code class="code-inline" style="color:#86efac;border-color:rgba(34,197,94,.2);">php zero make:controller ProductController</code>
                    <code class="code-inline" style="color:#86efac;border-color:rgba(34,197,94,.2);">php zero make:model Product</code>
                </div>
            </div>
        </div>

        <div class="endpoint-group">
            <div class="endpoint-group-title">Available Endpoints</div>
            <div class="endpoint">
                <span class="method method-get">GET</span>
                <span class="path">/health</span>
                <span class="endpoint-desc">Health check</span>
            </div>
            <div class="endpoint">
                <span class="method method-post">POST</span>
                <span class="path">/api/login</span>
                <span class="endpoint-desc">Authenticate and receive a token</span>
            </div>
            <div class="endpoint">
                <span class="method method-get">GET</span>
                <span class="path">/api/users</span>
                <span class="endpoint-desc">List all users</span>
            </div>
            <div class="endpoint">
                <span class="method method-get">GET</span>
                <span class="path">/api/users/{id}</span>
                <span class="endpoint-desc">Get a single user</span>
            </div>
        </div>

        <div class="stats">
            <div class="stat">
                <span class="stat-label">Framework</span>
                <span class="stat-value">ZeroPing v<?= htmlspecialchars($version ?? '', ENT_QUOTES) ?></span>
            </div>
            <div class="stat">
                <span class="stat-label">PHP</span>
                <span class="stat-value"><?= htmlspecialchars($php ?? PHP_VERSION, ENT_QUOTES) ?></span>
            </div>
            <div class="stat">
                <span class="stat-label">Environment</span>
                <span class="stat-value"><?= htmlspecialchars(ucfirst($env ?? 'local'), ENT_QUOTES) ?></span>
            </div>
        </div>

        <div class="footer-links">
            <a href="https://zero-ping.duckdns.org/docs/introduction">Documentation</a>
            <a href="https://github.com/RITH-1437/ZeroPing">GitHub</a>
            <a href="https://zero-ping.duckdns.org/api">API Reference</a>
        </div>
    </main>
</body>
</html>
