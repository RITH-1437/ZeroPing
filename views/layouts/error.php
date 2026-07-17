<?php
/**
 * Shared error page layout for the ZeroPing starter application.
 *
 * Expected variables (provided by the error view that includes this file,
 * or injected by App\Core\Routing\Router::renderError):
 *   int    $code         HTTP status code
 *   string $title        Short human title (e.g. "Page Not Found")
 *   string $description  One-line description
 *   string $icon         Icon key: 404|401|403|419|429|500|503
 *   bool   $debug        Whether to show the developer debug panel
 *   string $message      Exception message (debug only)
 *   string $exception    Exception class (debug only)
 *   string $file         Exception file (debug only)
 *   int    $line         Exception line (debug only)
 *   array  $trace        Exception stack trace (debug only)
 *   string $requestUrl   Current request URI (debug only)
 *   string $requestMethod Current request method (debug only)
 *   string $environment  App environment (debug only)
 */
$code        = $code ?? 500;
$title       = $title ?? 'Something Went Wrong';
$description = $description ?? 'An unexpected error occurred.';
$icon        = $icon ?? '500';
$debug       = !empty($debug);

$homeUrl = rtrim((string) ($_ENV['APP_URL'] ?? 'http://localhost:1437'), '/');
$homeUrl = $homeUrl === '' ? '/' : $homeUrl;

$icons = [
    '404' => '<circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>',
    '401' => '<rect x="3" y="11" width="18" height="10" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>',
    '403' => '<path d="M12 2 4 5v6c0 5 3.5 8.5 8 11 4.5-2.5 8-6 8-11V5l-8-3z"/><line x1="9" y1="12" x2="15" y2="12"/>',
    '419' => '<circle cx="12" cy="12" r="9"/><polyline points="12 7 12 12 15 14"/>',
    '429' => '<circle cx="12" cy="13" r="8"/><path d="M12 9v4l2 2"/><line x1="9" y1="2" x2="15" y2="2"/>',
    '500' => '<rect x="8" y="8" width="8" height="8" rx="1"/><line x1="8" y1="16" x2="8" y2="20"/><line x1="16" y1="16" x2="16" y2="20"/><line x1="8" y1="6" x2="16" y2="6"/><line x1="12" y1="6" x2="12" y2="3"/>',
    '503' => '<rect x="3" y="4" width="18" height="7" rx="2"/><rect x="3" y="13" width="18" height="7" rx="2"/><line x1="7" y1="7.5" x2="7" y2="7.5"/><line x1="7" y1="16.5" x2="7" y2="16.5"/>',
];
$iconPath = $icons[$icon] ?? $icons['500'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars((string) $code, ENT_QUOTES) ?> &middot; <?= htmlspecialchars($title, ENT_QUOTES) ?> — ZeroPing</title>
    <style>
        :root {
            --bg: #070b14;
            --bg-soft: #0d1424;
            --card: #0e1626;
            --border: #1c2740;
            --text: #e8eef9;
            --muted: #93a1bd;
            --faint: #5d6b87;
            --primary: #22c55e;
            --primary-soft: rgba(34, 197, 94, .12);
            --term-bg: #050912;
        }
        * { box-sizing: border-box; }
        html, body { margin: 0; padding: 0; }
        body {
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            font-family: ui-sans-serif, system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
            background:
                radial-gradient(1100px 560px at 50% -12%, rgba(34, 197, 94, .16), transparent 62%),
                radial-gradient(820px 460px at 50% 116%, rgba(22, 163, 74, .09), transparent 60%),
                var(--bg);
            color: var(--text);
            padding: 48px 22px;
            -webkit-font-smoothing: antialiased;
            text-rendering: optimizeLegibility;
        }
        .zp-error {
            width: 100%; max-width: 640px; text-align: center;
            opacity: 0; transform: translateY(14px);
            animation: zp-fade .55s cubic-bezier(.22, 1, .36, 1) forwards;
        }
        @keyframes zp-fade { to { opacity: 1; transform: none; } }
        .zp-icon {
            width: 84px; height: 84px; margin: 0 auto 26px;
            display: grid; place-items: center; border-radius: 22px;
            background: var(--primary-soft);
            border: 1px solid rgba(34, 197, 94, .35);
            color: var(--primary);
            box-shadow: 0 0 34px -8px rgba(34, 197, 94, .55);
            animation: zp-float 5.5s ease-in-out infinite;
        }
        .zp-icon svg { width: 40px; height: 40px; stroke: currentColor; stroke-width: 1.8; fill: none; stroke-linecap: round; stroke-linejoin: round; }
        @keyframes zp-float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-9px); }
        }
        .zp-code {
            font-family: 'JetBrains Mono', 'Fira Code', 'Cascadia Code', Consolas, monospace;
            font-size: clamp(72px, 16vw, 124px); font-weight: 800; letter-spacing: -.04em;
            line-height: 1; margin: 0; color: #fff;
            text-shadow: 0 0 24px rgba(34, 197, 94, .45), 0 0 70px rgba(34, 197, 94, .22);
        }
        .zp-title {
            font-size: clamp(22px, 4vw, 30px); font-weight: 700; letter-spacing: -.02em;
            margin: 18px 0 0; color: #f4f7fc;
        }
        .zp-desc {
            color: var(--muted); font-size: 15px; line-height: 1.6;
            max-width: 440px; margin: 14px auto 0;
        }
        .zp-actions {
            display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;
            margin-top: 32px;
        }
        .zp-btn {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 12px 22px; border-radius: 11px; font-size: 14px; font-weight: 600;
            text-decoration: none; cursor: pointer; border: 1px solid transparent;
            transition: transform .18s ease, box-shadow .18s ease, background .18s ease, border-color .18s ease, color .18s ease;
        }
        .zp-btn-primary {
            color: #04130a;
            background: linear-gradient(135deg, #22c55e, #16a34a);
            box-shadow: 0 12px 30px -12px rgba(34, 197, 94, .7);
        }
        .zp-btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 18px 40px -12px rgba(34, 197, 94, .85);
        }
        .zp-btn-secondary {
            color: var(--text); background: var(--card); border-color: var(--border);
        }
        .zp-btn-secondary:hover {
            transform: translateY(-2px); border-color: rgba(34, 197, 94, .5);
            color: var(--primary);
        }
        .zp-footer {
            margin-top: 40px; color: var(--faint); font-size: 12px; letter-spacing: .04em;
            font-family: 'JetBrains Mono', 'Fira Code', Consolas, monospace;
        }
        .zp-footer span { color: var(--primary); }

        .zp-debug {
            margin-top: 36px; text-align: left;
            background: var(--term-bg); border: 1px solid var(--border);
            border-radius: 14px; overflow: hidden;
            box-shadow: 0 26px 70px -28px rgba(0, 0, 0, .8);
        }
        .zp-debug-bar {
            display: flex; align-items: center; gap: 8px;
            padding: 12px 16px; background: #0a1020; border-bottom: 1px solid var(--border);
            font-family: 'JetBrains Mono', 'Fira Code', Consolas, monospace; font-size: 12px; color: var(--muted);
        }
        .zp-debug-bar .dot { width: 10px; height: 10px; border-radius: 50%; }
        .zp-debug-bar .dot.r { background: #ff5f57; }
        .zp-debug-bar .dot.y { background: #febc2e; }
        .zp-debug-bar .dot.g { background: #28c840; }
        .zp-debug-summary {
            width: 100%; text-align: left; background: none; border: 0; cursor: pointer;
            color: var(--text); font: inherit; padding: 12px 16px;
            display: flex; align-items: center; justify-content: space-between; gap: 12px;
        }
        .zp-debug-summary:hover { color: var(--primary); }
        .zp-debug-summary .chev { transition: transform .2s ease; color: var(--faint); }
        .zp-debug.open .chev { transform: rotate(90deg); }
        .zp-debug-body {
            display: none; padding: 4px 16px 16px;
            font-family: 'JetBrains Mono', 'Fira Code', Consolas, monospace; font-size: 12.5px; line-height: 1.7;
        }
        .zp-debug.open .zp-debug-body { display: block; }
        .zp-debug-body .row { padding: 10px 0; border-top: 1px solid var(--border); }
        .zp-debug-body .row:first-child { border-top: 0; }
        .zp-debug-body .k { color: var(--faint); display: block; margin-bottom: 3px; }
        .zp-debug-body .v { color: #cdd6e8; word-break: break-word; }
        .zp-debug-body pre {
            margin: 6px 0 0; padding: 12px; background: #04070f; border: 1px solid var(--border);
            border-radius: 10px; overflow-x: auto; color: #9fb3d1; max-height: 320px;
        }
        @media (prefers-reduced-motion: reduce) {
            .zp-error { animation: none; opacity: 1; transform: none; }
            .zp-icon { animation: none; }
        }
        @media (max-width: 480px) {
            .zp-actions { flex-direction: column; }
            .zp-btn { justify-content: center; }
        }
    </style>
</head>
<body>
    <main class="zp-error">
        <div class="zp-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24"><?= $iconPath ?></svg>
        </div>

        <h1 class="zp-code"><?= htmlspecialchars((string) $code, ENT_QUOTES) ?></h1>
        <h2 class="zp-title"><?= htmlspecialchars($title, ENT_QUOTES) ?></h2>
        <p class="zp-desc"><?= htmlspecialchars($description, ENT_QUOTES) ?></p>

        <div class="zp-actions">
            <a class="zp-btn zp-btn-primary" href="<?= htmlspecialchars($homeUrl, ENT_QUOTES) ?>">Return Home</a>
            <a class="zp-btn zp-btn-secondary" href="javascript:history.back()">Go Back</a>
        </div>

        <?php if ($debug): ?>
            <div class="zp-debug" id="zp-debug">
                <div class="zp-debug-bar">
                    <span class="dot r"></span><span class="dot y"></span><span class="dot g"></span>
                    <span style="margin-left:6px;">Debug &middot; ZeroPing <?= htmlspecialchars(defined('App\\Core\\Application\\App::VERSION') ? \App\Core\Application\App::VERSION : '', ENT_QUOTES) ?></span>
                </div>
                <button type="button" class="zp-debug-summary" aria-expanded="false" onclick="(function(b){var d=document.getElementById('zp-debug');var o=d.classList.toggle('open');b.setAttribute('aria-expanded',o?'true':'false');})(this)">
                    <span><strong style="color:#f87171;"><?= htmlspecialchars($exception ?: 'Error', ENT_QUOTES) ?></strong>
                        &nbsp;<?= htmlspecialchars($message ?: '', ENT_QUOTES) ?></span>
                    <span class="chev">&#9656;</span>
                </button>
                <div class="zp-debug-body">
                    <div class="row">
                        <span class="k">Exception</span>
                        <span class="v"><?= htmlspecialchars($exception ?: '—', ENT_QUOTES) ?></span>
                    </div>
                    <div class="row">
                        <span class="k">Message</span>
                        <span class="v"><?= htmlspecialchars($message ?: '—', ENT_QUOTES) ?></span>
                    </div>
                    <div class="row">
                        <span class="k">File</span>
                        <span class="v"><?= htmlspecialchars($file ?: '—', ENT_QUOTES) ?>:<?= htmlspecialchars((string) $line, ENT_QUOTES) ?></span>
                    </div>
                    <div class="row">
                        <span class="k">Request</span>
                        <span class="v"><?= htmlspecialchars($requestMethod ?? 'GET', ENT_QUOTES) ?> <?= htmlspecialchars($requestUrl ?? '/', ENT_QUOTES) ?></span>
                    </div>
                    <div class="row">
                        <span class="k">Environment</span>
                        <span class="v"><?= htmlspecialchars($environment ?? 'production', ENT_QUOTES) ?></span>
                    </div>
                    <div class="row">
                        <span class="k">Stack Trace</span>
                        <pre><code><?php
                            $traceOut = '';
                            foreach (array_slice($trace ?? [], 0, 20) as $frame) {
                                $traceOut .= (isset($frame['file']) ? $frame['file'] : '[internal]')
                                    . ':' . ($frame['line'] ?? '?')
                                    . '  ' . ($frame['class'] ?? '')
                                    . ($frame['type'] ?? '')
                                    . ($frame['function'] ?? '') . "()\n";
                            }
                            echo htmlspecialchars($traceOut, ENT_QUOTES);
                        ?></code></pre>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="zp-footer">ZeroPing <span>&bull;</span> Modern PHP Framework</div>
    </main>
</body>
</html>
