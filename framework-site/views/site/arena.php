<?php
require_once __DIR__ . '/../components/component.php';
/** @var array<int, array<string, mixed>> $stats */
/** @var array<int, array<string, string>> $ecosystem */
/** @var array<int, array<string, string>> $playground */
/** @var array<int, array<string, string>> $packages */
/** @var array<int, array<string, mixed>> $roadmap */
/** @var array<int, array<string, string>> $commands */
?>

<!-- SVG sprite: symbols are reused throughout the page and add no network requests. -->
<svg class="arena-icon-sprite" aria-hidden="true" width="0" height="0">
    <symbol id="arena-icon-arrow" viewBox="0 0 24 24"><path d="M5 12h14m-5-5 5 5-5 5"/></symbol>
    <symbol id="arena-icon-copy" viewBox="0 0 24 24"><rect x="9" y="9" width="11" height="11" rx="2"/><path d="M15 9V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v7a2 2 0 0 0 2 2h3"/></symbol>
    <symbol id="arena-icon-play" viewBox="0 0 24 24"><path d="m8 5 11 7-11 7V5Z"/></symbol>
    <symbol id="arena-icon-framework" viewBox="0 0 24 24"><path d="m12 3 8 4.5v9L12 21l-8-4.5v-9L12 3Z"/><path d="m4.5 7.8 7.5 4.4 7.5-4.4M12 12.2V21"/></symbol>
    <symbol id="arena-icon-terminal" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="m7 9 3 3-3 3m5 0h5"/></symbol>
    <symbol id="arena-icon-arena" viewBox="0 0 24 24"><circle cx="12" cy="12" r="8"/><circle cx="12" cy="12" r="3"/><path d="M12 2v3m0 14v3M2 12h3m14 0h3"/></symbol>
    <symbol id="arena-icon-package" viewBox="0 0 24 24"><path d="m12 3 8 4.5v9L12 21l-8-4.5v-9L12 3Z"/><path d="m8 5.3 8 4.5v5M4.5 7.8l7.5 4.4 7.5-4.4"/></symbol>
    <symbol id="arena-icon-book" viewBox="0 0 24 24"><path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H11v16H6.5A2.5 2.5 0 0 0 4 21.5v-16ZM20 5.5A2.5 2.5 0 0 0 17.5 3H13v16h4.5a2.5 2.5 0 0 1 2.5 2.5v-16Z"/></symbol>
    <symbol id="arena-icon-code" viewBox="0 0 24 24"><path d="m8 7-5 5 5 5m8-10 5 5-5 5m-2-13-4 16"/></symbol>
    <symbol id="arena-icon-spark" viewBox="0 0 24 24"><path d="m12 2 1.3 5.2L18 9l-4.7 1.8L12 16l-1.3-5.2L6 9l4.7-1.8L12 2Zm7 12 .7 2.3L22 17l-2.3.7L19 20l-.7-2.3L16 17l2.3-.7L19 14Z"/></symbol>
    <symbol id="arena-icon-layout" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></symbol>
    <symbol id="arena-icon-puzzle" viewBox="0 0 24 24"><path d="M9 3H4a1 1 0 0 0-1 1v5a3 3 0 1 1 0 6v5a1 1 0 0 0 1 1h5a3 3 0 1 1 6 0h5a1 1 0 0 0 1-1v-5a3 3 0 1 1 0-6V4a1 1 0 0 0-1-1h-5a3 3 0 1 1-6 0Z"/></symbol>
    <symbol id="arena-icon-store" viewBox="0 0 24 24"><path d="M4 10v10h16V10M3 4h18l-2 6H5L3 4Z"/><path d="M8 14h3v6"/></symbol>
    <symbol id="arena-icon-graduation" viewBox="0 0 24 24"><path d="m2 8 10-5 10 5-10 5L2 8Z"/><path d="M6 10.5V16c3 3 9 3 12 0v-5.5M22 8v7"/></symbol>
    <symbol id="arena-icon-users" viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm8-1a3 3 0 1 0 0-6m5 17v-2a4 4 0 0 0-3-3.9"/></symbol>
    <symbol id="arena-icon-route" viewBox="0 0 24 24"><circle cx="6" cy="18" r="3"/><circle cx="18" cy="6" r="3"/><path d="M6 15V9a3 3 0 0 1 3-3h6m-3 12h6"/></symbol>
    <symbol id="arena-icon-history" viewBox="0 0 24 24"><path d="M3 12a9 9 0 1 0 3-6.7L3 8"/><path d="M3 3v5h5m4-1v5l3 2"/></symbol>
    <symbol id="arena-icon-gauge" viewBox="0 0 24 24"><path d="M4.2 19a9 9 0 1 1 15.6 0M12 12l4-4"/><path d="M7 19h10"/></symbol>
    <symbol id="arena-icon-bolt" viewBox="0 0 24 24"><path d="m13 2-9 12h7l-1 8 9-12h-7l1-8Z"/></symbol>
    <symbol id="arena-icon-tools" viewBox="0 0 24 24"><path d="m14 6 4-4 4 4-4 4M12 8l-9 9v4h4l9-9M3 3l18 18"/></symbol>
    <symbol id="arena-icon-chart" viewBox="0 0 24 24"><path d="M4 20V10m5 10V4m6 16v-7m5 7V7"/></symbol>
    <symbol id="arena-icon-memory" viewBox="0 0 24 24"><rect x="5" y="5" width="14" height="14" rx="2"/><path d="M9 9h6v6H9zM9 2v3m6-3v3M9 19v3m6-3v3M2 9h3m-3 6h3m14-6h3m-3 6h3"/></symbol>
    <symbol id="arena-icon-database" viewBox="0 0 24 24"><ellipse cx="12" cy="5" rx="8" ry="3"/><path d="M4 5v7c0 1.7 3.6 3 8 3s8-1.3 8-3V5M4 12v7c0 1.7 3.6 3 8 3s8-1.3 8-3v-7"/></symbol>
    <symbol id="arena-icon-globe" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c3 3 3 15 0 18m0-18c-3 3-3 15 0 18"/></symbol>
    <symbol id="arena-icon-shield" viewBox="0 0 24 24"><path d="M12 3 4 6v6c0 5 3.5 8 8 9 4.5-1 8-4 8-9V6l-8-3Z"/><path d="m9 12 2 2 4-4"/></symbol>
    <symbol id="arena-icon-key" viewBox="0 0 24 24"><circle cx="8" cy="15" r="4"/><path d="m11 12 9-9m-4 4 3 3m-7 1 3 3"/></symbol>
    <symbol id="arena-icon-mail" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m4 7 8 6 8-6"/></symbol>
    <symbol id="arena-icon-queue" viewBox="0 0 24 24"><path d="M4 6h16M4 12h10M4 18h7m7-3 3 3-3 3"/></symbol>
    <symbol id="arena-icon-folder" viewBox="0 0 24 24"><path d="M3 6a2 2 0 0 1 2-2h5l2 3h7a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6Z"/></symbol>
    <symbol id="arena-icon-check" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="m8 12 3 3 5-6"/></symbol>
    <symbol id="arena-icon-clock" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></symbol>
    <symbol id="arena-icon-bell" viewBox="0 0 24 24"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4"/></symbol>
    <symbol id="arena-icon-search" viewBox="0 0 24 24"><circle cx="10.5" cy="10.5" r="6.5"/><path d="m15.5 15.5 5 5"/></symbol>
    <symbol id="arena-icon-braces" viewBox="0 0 24 24"><path d="M8 3H6a2 2 0 0 0-2 2v4l-2 3 2 3v4a2 2 0 0 0 2 2h2m8-18h2a2 2 0 0 1 2 2v4l2 3-2 3v4a2 2 0 0 1-2 2h-2"/></symbol>
    <symbol id="arena-icon-flask" viewBox="0 0 24 24"><path d="M9 3h6m-5 0v6L4 19a2 2 0 0 0 2 3h12a2 2 0 0 0 2-3L14 9V3M7 16h10"/></symbol>
    <symbol id="arena-icon-pages" viewBox="0 0 24 24"><rect x="4" y="6" width="16" height="14" rx="2"/><path d="M8 3h8M8 11h8m-8 4h5"/></symbol>
</svg>

<div class="arena-page">
    <section class="arena-hero" aria-labelledby="arena-hero-title">
        <div class="arena-hero-grid" aria-hidden="true"></div>
        <div class="arena-blob arena-blob--one" aria-hidden="true"></div>
        <div class="arena-blob arena-blob--two" aria-hidden="true"></div>
        <div class="arena-shell arena-hero-layout">
            <div class="arena-hero-copy">
                <a class="arena-eyebrow" href="#roadmap"><span></span> Arena preview · The ecosystem is taking shape <b>View roadmap →</b></a>
                <h1 id="arena-hero-title">Build Faster.<br><span>Ship Smarter.</span></h1>
                <p>Everything you need to build, scale, and maintain modern PHP applications—one coherent, lightweight ecosystem.</p>
                <div class="arena-hero-actions">
                    <a class="arena-button arena-button--primary" href="/getting-started">Get Started <svg aria-hidden="true"><use href="#arena-icon-arrow"></use></svg></a>
                    <a class="arena-button arena-button--secondary" href="#ecosystem">Explore Ecosystem</a>
                </div>
                <div class="arena-hero-trust" aria-label="Platform principles">
                    <span><svg aria-hidden="true"><use href="#arena-icon-check"></use></svg>MIT licensed</span>
                    <span><svg aria-hidden="true"><use href="#arena-icon-check"></use></svg>PHP 8.1+</span>
                    <span><svg aria-hidden="true"><use href="#arena-icon-check"></use></svg>Composer native</span>
                </div>
            </div>
            <div class="arena-hero-terminal" aria-label="ZeroPing command line preview">
                <div class="arena-terminal-bar"><div class="arena-window-dots" aria-hidden="true"><span></span><span></span><span></span></div><p>zero — my-app</p><span>PHP</span></div>
                <div class="arena-terminal-body">
                    <p><span class="arena-prompt">➜</span> <span class="arena-path">~</span> <strong>php zero new my-app</strong></p>
                    <div class="arena-terminal-output" data-terminal-output>
                        <p><i>◆</i> Creating ZeroPing application...</p>
                        <p><i>◆</i> Installing framework <b>v<?= htmlspecialchars(\App\Core\Application\App::VERSION, ENT_QUOTES, 'UTF-8') ?></b></p>
                        <p><i>◆</i> Configuring SQLite database</p>
                        <p><i>◆</i> Generating application key</p>
                        <p class="is-success"><i>✓</i> Application ready in <b>2.4s</b></p>
                    </div>
                    <p class="arena-terminal-next"><span class="arena-prompt">➜</span> <span class="arena-path">~/my-app</span> <strong>php zero serve</strong><span class="arena-terminal-cursor" aria-hidden="true"></span></p>
                </div>
                <div class="arena-terminal-status"><span><i></i> Server ready</span><span>localhost:1437</span></div>
            </div>
        </div>
    </section>

    <section class="arena-stats" aria-label="ZeroPing project statistics">
        <div class="arena-shell arena-stats-grid">
            <?php foreach ($stats as $stat): ?>
                <div class="arena-stat">
                    <strong data-counter="<?= (int) $stat['number'] ?>" data-format="<?= htmlspecialchars((string) $stat['format'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars((string) $stat['value'], ENT_QUOTES, 'UTF-8') ?></strong>
                    <span><?= htmlspecialchars((string) $stat['label'], ENT_QUOTES, 'UTF-8') ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section id="ecosystem" class="arena-section arena-shell" aria-labelledby="ecosystem-title" data-arena-reveal>
        <div class="arena-section-heading">
            <div><span class="arena-kicker">Explore the platform</span><h2 id="ecosystem-title">One ecosystem. Every stage.</h2></div>
            <p>Move from first route to production workflow without switching mental models or accepting unnecessary weight.</p>
        </div>
        <div class="arena-ecosystem-grid">
            <?php foreach ($ecosystem as $item) render_component('arena-ecosystem-card', ['item' => $item]); ?>
        </div>
    </section>

    <section id="playground" class="arena-section arena-section--tinted" aria-labelledby="playground-title">
        <div class="arena-shell" data-arena-reveal>
            <div class="arena-section-heading">
                <div><span class="arena-kicker">ZeroPing Arena</span><h2 id="playground-title">A playground for serious work.</h2></div>
                <p>Inspect how the framework behaves. Prototype features against guided, isolated scenarios. Every unfinished tool is clearly labeled.</p>
            </div>
            <div id="benchmark-runner" class="arena-tool-grid">
                <?php foreach ($playground as $tool) render_component('arena-playground-card', ['tool' => $tool]); ?>
            </div>
            <p class="arena-method-note"><svg aria-hidden="true"><use href="#arena-icon-flask"></use></svg><span><strong>Transparent by default.</strong> Preview values demonstrate the interface—not independent performance claims. Published benchmarks will include hardware, runtime, dataset, and source methodology.</span></p>
        </div>
    </section>

    <section id="packages" class="arena-section arena-shell" aria-labelledby="packages-title" data-arena-reveal>
        <div class="arena-section-heading">
            <div><span class="arena-kicker">Official packages</span><h2 id="packages-title">Add capability, not complexity.</h2></div>
            <p>Packages share contracts, providers, configuration, and command conventions. Install only what the application needs.</p>
        </div>
        <div class="arena-package-toolbar">
            <div class="arena-package-summary"><span>14</span> modules in the official roadmap</div>
            <a href="/packages">Package architecture <span aria-hidden="true">→</span></a>
        </div>
        <div class="arena-package-grid">
            <?php foreach ($packages as $package) render_component('arena-package-card', ['package' => $package]); ?>
        </div>
    </section>

    <section id="developer-experience" class="arena-section arena-section--tinted" aria-labelledby="dx-title">
        <div class="arena-shell arena-dx-layout" data-arena-reveal>
            <div class="arena-dx-copy">
                <span class="arena-kicker">Developer experience</span>
                <h2 id="dx-title">From idea to working code, without ceremony.</h2>
                <p>The Zero CLI keeps the routine work visible, predictable, and close to standard PHP tooling.</p>
                <ul>
                    <li><svg aria-hidden="true"><use href="#arena-icon-check"></use></svg>Readable generated code</li>
                    <li><svg aria-hidden="true"><use href="#arena-icon-check"></use></svg>Production checks built in</li>
                    <li><svg aria-hidden="true"><use href="#arena-icon-check"></use></svg>No proprietary package format</li>
                </ul>
                <a href="/docs/cli">Explore the CLI reference <span aria-hidden="true">→</span></a>
            </div>
            <div class="arena-command-panel">
                <div class="arena-command-tabs" role="tablist" aria-label="ZeroPing CLI examples">
                    <?php foreach ($commands as $index => $command): ?>
                        <button id="arena-tab-<?= htmlspecialchars($command['id'], ENT_QUOTES, 'UTF-8') ?>" role="tab" aria-selected="<?= $index === 0 ? 'true' : 'false' ?>" aria-controls="arena-command-<?= htmlspecialchars($command['id'], ENT_QUOTES, 'UTF-8') ?>" tabindex="<?= $index === 0 ? '0' : '-1' ?>" data-command-tab="<?= htmlspecialchars($command['id'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($command['label'], ENT_QUOTES, 'UTF-8') ?></button>
                    <?php endforeach; ?>
                </div>
                <?php foreach ($commands as $index => $command): ?>
                    <div id="arena-command-<?= htmlspecialchars($command['id'], ENT_QUOTES, 'UTF-8') ?>" class="arena-command-content" role="tabpanel" aria-labelledby="arena-tab-<?= htmlspecialchars($command['id'], ENT_QUOTES, 'UTF-8') ?>" <?= $index !== 0 ? 'hidden' : '' ?> data-command-panel="<?= htmlspecialchars($command['id'], ENT_QUOTES, 'UTF-8') ?>">
                        <div><span>Terminal</span><button type="button" data-arena-copy="<?= htmlspecialchars($command['command'], ENT_QUOTES, 'UTF-8') ?>"><svg aria-hidden="true"><use href="#arena-icon-copy"></use></svg><span>Copy</span></button></div>
                        <pre><code><span>$</span> <?= htmlspecialchars($command['command'], ENT_QUOTES, 'UTF-8') ?></code></pre>
                        <h3><?= htmlspecialchars($command['title'], ENT_QUOTES, 'UTF-8') ?></h3>
                        <p><?= htmlspecialchars($command['detail'], ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section id="performance" class="arena-section arena-shell" aria-labelledby="performance-title" data-arena-reveal>
        <div class="arena-performance-card">
            <div class="arena-performance-copy">
                <span class="arena-kicker">Performance philosophy</span>
                <h2 id="performance-title">Measure what your users feel.</h2>
                <p>Fast defaults matter, but context matters more. Arena will make benchmark inputs visible and focus on route dispatch, memory, ORM work, and application startup.</p>
                <a href="/docs/performance">Read the performance guide <span aria-hidden="true">→</span></a>
            </div>
            <div class="arena-metric-stack" aria-label="Illustrative performance dimensions">
                <div><span><i></i>Route dispatch</span><strong>Latency</strong><b style="--metric:88%"></b></div>
                <div><span><i></i>Application boot</span><strong>Startup</strong><b style="--metric:74%"></b></div>
                <div><span><i></i>ORM hydration</span><strong>Throughput</strong><b style="--metric:64%"></b></div>
                <div><span><i></i>Request lifecycle</span><strong>Memory</strong><b style="--metric:51%"></b></div>
            </div>
        </div>
    </section>

    <section id="community" class="arena-section arena-section--tinted" aria-labelledby="community-title">
        <div class="arena-shell" data-arena-reveal>
            <div class="arena-section-heading">
                <div><span class="arena-kicker">Built in the open</span><h2 id="community-title">The ecosystem belongs to its builders.</h2></div>
                <p>Ask questions, shape priorities, share projects, and help make modern PHP development calmer.</p>
            </div>
            <div class="arena-community-grid">
                <a href="https://github.com/RITH-1437/ZeroPing" target="_blank" rel="noopener noreferrer"><span class="arena-icon"><svg aria-hidden="true"><use href="#arena-icon-code"></use></svg></span><strong>GitHub</strong><p>Read source, report issues, and contribute.</p><b>View repository ↗</b></a>
                <a href="https://github.com/RITH-1437/ZeroPing/discussions" target="_blank" rel="noopener noreferrer"><span class="arena-icon"><svg aria-hidden="true"><use href="#arena-icon-users"></use></svg></span><strong>Discussions</strong><p>Ask questions and propose ecosystem ideas.</p><b>Join a conversation ↗</b></a>
                <a href="/community"><span class="arena-icon"><svg aria-hidden="true"><use href="#arena-icon-spark"></use></svg></span><strong>Contributors</strong><p>Meet the people improving ZeroPing.</p><b>Community hub →</b></a>
                <a href="/sponsors"><span class="arena-icon"><svg aria-hidden="true"><use href="#arena-icon-shield"></use></svg></span><strong>Sponsors</strong><p>Support sustainable open-source maintenance.</p><b>Support ZeroPing →</b></a>
                <a href="/community"><span class="arena-icon"><svg aria-hidden="true"><use href="#arena-icon-graduation"></use></svg></span><strong>Hall of Fame</strong><p>Recognizing meaningful community impact.</p><b>Coming soon</b></a>
                <a href="/changelog"><span class="arena-icon"><svg aria-hidden="true"><use href="#arena-icon-history"></use></svg></span><strong>Latest releases</strong><p>Current version v<?= htmlspecialchars(\App\Core\Application\App::VERSION, ENT_QUOTES, 'UTF-8') ?> and its release notes.</p><b>Read changelog →</b></a>
            </div>
            <div class="arena-discord-note"><span class="arena-status arena-status--soon">Planned</span><p><strong>Discord will open when the community can be supported well.</strong> Until then, GitHub Discussions is the canonical, searchable support channel.</p></div>
        </div>
    </section>

    <section id="roadmap" class="arena-section arena-shell" aria-labelledby="roadmap-title" data-arena-reveal>
        <div class="arena-section-heading">
            <div><span class="arena-kicker">Product roadmap</span><h2 id="roadmap-title">Deliberate progress, clearly shared.</h2></div>
            <p>The sequence protects the lightweight core: package architecture first, platform services only when the foundation is ready.</p>
        </div>
        <ol class="arena-roadmap">
            <?php foreach ($roadmap as $milestone): ?>
                <li class="arena-roadmap-item arena-roadmap-item--<?= htmlspecialchars((string) $milestone['tone'], ENT_QUOTES, 'UTF-8') ?>">
                    <div class="arena-roadmap-marker" aria-hidden="true"><span></span></div>
                    <article>
                        <div><strong><?= htmlspecialchars((string) $milestone['version'], ENT_QUOTES, 'UTF-8') ?></strong><span><?= htmlspecialchars((string) $milestone['status'], ENT_QUOTES, 'UTF-8') ?></span></div>
                        <h3><?= htmlspecialchars((string) $milestone['label'], ENT_QUOTES, 'UTF-8') ?></h3>
                        <ul><?php foreach ($milestone['items'] as $item): ?><li><?= htmlspecialchars((string) $item, ENT_QUOTES, 'UTF-8') ?></li><?php endforeach; ?></ul>
                    </article>
                </li>
            <?php endforeach; ?>
        </ol>
        <div class="arena-roadmap-action"><a class="arena-button arena-button--secondary" href="/roadmap">Open the full roadmap <svg aria-hidden="true"><use href="#arena-icon-arrow"></use></svg></a></div>
    </section>

    <section class="arena-cta arena-shell" aria-labelledby="arena-cta-title" data-arena-reveal>
        <div class="arena-cta-inner">
            <div><span class="arena-kicker">Your next application</span><h2 id="arena-cta-title">Start small. Scale intentionally.</h2><p>Build the first route today. Add the ecosystem only when your application asks for it.</p></div>
            <div><a class="arena-button arena-button--primary" href="/installation">Install ZeroPing <svg aria-hidden="true"><use href="#arena-icon-arrow"></use></svg></a><a class="arena-button arena-button--secondary" href="https://github.com/RITH-1437/ZeroPing" target="_blank" rel="noopener noreferrer">View on GitHub <span aria-hidden="true">↗</span></a></div>
        </div>
    </section>
</div>
