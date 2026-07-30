<footer class="site-footer mt-8 border-t border-zp-border">
    <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
        <div class="grid gap-10 md:grid-cols-[1.25fr_repeat(3,minmax(0,1fr))]">
            <div>
                <a href="/" class="brand-mark focus-ring" aria-label="ZeroPing home">
                    <img src="/assets/images/mascot.svg" alt="" width="32" height="32" class="h-8 w-8" loading="lazy">
                    <span>ZeroPing</span>
                </a>
                <p class="mt-4 max-w-xs text-sm leading-6 text-zp-desc">A focused PHP framework for shipping reliable applications with less ceremony.</p>
                <div class="mt-5 flex items-center gap-2">
                    <span class="status-dot" aria-hidden="true"></span><span class="text-xs font-medium text-zp-muted">Open source · MIT licensed</span>
                </div>
            </div>
            <div>
                <h2 class="footer-heading">Start building</h2>
                <ul class="footer-links"><li><a href="/installation">Installation</a></li><li><a href="/getting-started">Getting started</a></li><li><a href="/examples">Examples</a></li><li><a href="/features">Features</a></li></ul>
            </div>
            <div>
                <h2 class="footer-heading">Resources</h2>
                <ul class="footer-links"><li><a href="/docs/introduction">Documentation</a></li><li><a href="/api">API reference</a></li><li><a href="/packages">Packages</a></li><li><a href="/changelog">Changelog</a></li></ul>
            </div>
            <div>
                <h2 class="footer-heading">Community</h2>
                <ul class="footer-links"><li><a href="/roadmap">Roadmap</a></li><li><a href="/community">Community</a></li><li><a href="/sponsors">Sponsors</a></li><li><a href="https://github.com/RITH-1437/ZeroPing" target="_blank" rel="noopener noreferrer">GitHub <span aria-hidden="true">↗</span></a></li></ul>
            </div>
        </div>
        <div class="mt-12 flex flex-col gap-3 border-t border-zp-border pt-6 text-xs text-zp-muted sm:flex-row sm:items-center sm:justify-between">
            <p>© <?= date('Y') ?> ZeroPing. Built in the open.</p>
            <div class="flex flex-wrap gap-x-5 gap-y-2"><a href="/blog">Blog <span class="footer-soon">Soon</span></a><a href="/up">System status</a><a href="https://github.com/RITH-1437/ZeroPing/blob/main/SECURITY.md" target="_blank" rel="noopener noreferrer">Security</a></div>
        </div>
    </div>
</footer>
