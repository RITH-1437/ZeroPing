<?php
$version = 'v' . \App\Core\Application\App::VERSION;
$year = date('Y');
?>
<footer class="arena-terminal-footer" aria-labelledby="arena-footer-title">
    <div class="arena-footer-glow" aria-hidden="true"></div>
    <div class="arena-shell arena-footer-shell">
        <div class="arena-footer-window">
            <div class="arena-terminal-bar">
                <div class="arena-window-dots" aria-hidden="true"><span></span><span></span><span></span></div>
                <p>zeroping@arena:~/ecosystem</p>
                <span class="arena-terminal-secure"><i></i> public</span>
            </div>
            <div class="arena-footer-content">
                <p class="arena-terminal-command"><span>➜</span> <strong>~</strong> zero about</p>
                <h2 id="arena-footer-title" class="sr-only">ZeroPing — Modern PHP Framework</h2>
                <pre class="arena-ascii-logo" aria-hidden="true">███████╗███████╗██████╗  ██████╗ ██████╗ ██╗███╗   ██╗ ██████╗
╚══███╔╝██╔════╝██╔══██╗██╔═══██╗██╔══██╗██║████╗  ██║██╔════╝
  ███╔╝ █████╗  ██████╔╝██║   ██║██████╔╝██║██╔██╗ ██║██║  ███╗
 ███╔╝  ██╔══╝  ██╔══██╗██║   ██║██╔═══╝ ██║██║╚██╗██║██║   ██║
███████╗███████╗██║  ██║╚██████╔╝██║     ██║██║ ╚████║╚██████╔╝
╚══════╝╚══════╝╚═╝  ╚═╝ ╚═════╝ ╚═╝     ╚═╝╚═╝  ╚═══╝ ╚═════╝</pre>
                <p class="arena-footer-tagline">Modern PHP Framework<span class="arena-terminal-cursor" aria-hidden="true"></span></p>
                <div class="arena-footer-meta">
                    <div><span>VERSION</span><strong><?= htmlspecialchars($version, ENT_QUOTES, 'UTF-8') ?></strong></div>
                    <div><span>LICENSE</span><strong>MIT</strong></div>
                    <div><span>RUNTIME</span><strong>PHP 8.1+</strong></div>
                    <div><span>STATUS</span><strong class="arena-online"><i></i> Open source</strong></div>
                </div>
                <nav class="arena-footer-links" aria-label="Footer navigation">
                    <a href="https://github.com/RITH-1437/ZeroPing" target="_blank" rel="noopener noreferrer">GitHub <span class="sr-only">(opens in a new tab)</span><span aria-hidden="true">↗</span></a>
                    <a href="/docs/introduction">Documentation</a>
                    <a href="/changelog">Release notes</a>
                    <a href="/roadmap">Roadmap</a>
                    <a href="https://github.com/RITH-1437/ZeroPing/blob/main/LICENSE" target="_blank" rel="noopener noreferrer">License <span class="sr-only">(opens in a new tab)</span><span aria-hidden="true">↗</span></a>
                </nav>
                <div class="arena-footer-bottom">
                    <p>Current <?= htmlspecialchars($version, ENT_QUOTES, 'UTF-8') ?> · © <?= htmlspecialchars($year, ENT_QUOTES, 'UTF-8') ?> ZeroPing</p>
                </div>
            </div>
        </div>
    </div>
</footer>
