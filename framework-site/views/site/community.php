<?php require_once __DIR__ . '/../components/component.php'; ?>
<section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8" data-animate>
    <?php render_component('breadcrumb', ['items' => [['label' => 'Home', 'href' => '/'], ['label' => 'Community']]]); ?>
    <div class="text-center">
        <h1 class="mt-6 font-display text-4xl sm:text-5xl font-bold tracking-tight text-zp-ink">Join the Community</h1>
        <p class="mt-4 max-w-2xl mx-auto text-zp-desc">ZeroPing is built in the open. Connect with other developers, ask questions, share what you build, and help shape the framework.</p>
    </div>

    <!-- Primary channels -->
    <div class="mt-14 grid sm:grid-cols-2 gap-5" data-animate-stagger>
        <a href="<?= htmlspecialchars($discussionsUrl ?? '', ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener"
           class="group relative rounded-2xl border border-zp-border bg-zp-surface/50 p-6 hover:bg-zp-surface hover:border-cyan-500/20 hover:shadow-lg hover:shadow-cyan-500/5 transition-all duration-300">
            <div class="absolute inset-0 rounded-2xl bg-zp-primary/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="relative">
                <div class="inline-flex items-center justify-center h-11 w-11 rounded-xl bg-zp-surface border border-zp-border text-zp-link transition-all duration-300 group-hover:scale-110 group-hover:border-cyan-500/40">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm8.706-1.442c1.146-.573 2.437.463 2.126 1.706l-.709 2.836.042-.02a1.75 1.75 0 011.295-3.117l.787-3.152a1.75 1.75 0 013.417-.315l.787 3.152a1.75 1.75 0 011.295 3.117l-.042.02.709-2.836c.311-1.243-.98-2.279-2.126-1.706L12 13.943l-1.294-3.385z"/></svg>
                </div>
                <h3 class="mt-5 text-xl font-semibold text-zp-ink group-hover:text-zp-link transition-colors">GitHub Discussions</h3>
                <p class="mt-2 text-sm text-zp-desc leading-relaxed">Ask questions, share ideas, and discuss proposals with maintainers and the community.</p>
                <span class="mt-4 inline-flex items-center gap-1 text-sm font-medium text-zp-link">Start a discussion
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </span>
            </div>
        </a>

        <a href="<?= htmlspecialchars($repositoryUrl ?? '', ENT_QUOTES, 'UTF-8') ?>/issues" target="_blank" rel="noopener"
           class="group relative rounded-2xl border border-zp-border bg-zp-surface/50 p-6 hover:bg-zp-surface hover:border-cyan-500/20 hover:shadow-lg hover:shadow-cyan-500/5 transition-all duration-300">
            <div class="absolute inset-0 rounded-2xl bg-zp-primary/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="relative">
                <div class="inline-flex items-center justify-center h-11 w-11 rounded-xl bg-zp-surface border border-zp-border text-zp-link transition-all duration-300 group-hover:scale-110 group-hover:border-cyan-500/40">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                </div>
                <h3 class="mt-5 text-xl font-semibold text-zp-ink group-hover:text-zp-link transition-colors">Issues &amp; Support</h3>
                <p class="mt-2 text-sm text-zp-desc leading-relaxed">Report bugs, request features, and track ongoing work in the public issue tracker.</p>
                <span class="mt-4 inline-flex items-center gap-1 text-sm font-medium text-zp-link">Browse issues
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </span>
            </div>
        </a>
    </div>

    <!-- Discord -->
    <div class="mt-5 rounded-2xl border border-zp-border bg-zp-surface/50 p-6 flex items-start gap-4">
        <div class="flex items-center justify-center h-11 w-11 rounded-xl bg-zp-surface border border-zp-border text-zp-link shrink-0">
            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M20.317 4.369a19.79 19.79 0 0 0-4.885-1.515.074.074 0 0 0-.079.037c-.21.375-.444.864-.608 1.25a18.27 18.27 0 0 0-5.487 0 12.6 12.6 0 0 0-.617-1.25.077.077 0 0 0-.079-.037A19.736 19.736 0 0 0 3.677 4.37a.07.07 0 0 0-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 0 0 .031.057 19.9 19.9 0 0 0 5.993 3.03.078.078 0 0 0 .084-.028c.462-.63.874-1.295 1.226-1.994a.076.076 0 0 0-.041-.106 13.1 13.1 0 0 1-1.872-.892.077.077 0 0 1-.008-.128c.126-.094.252-.192.372-.291a.074.074 0 0 1 .077-.01c3.928 1.793 8.18 1.793 12.062 0a.074.074 0 0 1 .078.009c.12.099.246.198.373.292a.077.077 0 0 1-.006.127c-.598.349-1.225.645-1.873.892a.077.077 0 0 0-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 0 0 .084.028 19.84 19.84 0 0 0 6.002-3.03.077.077 0 0 0 .032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 0 0-.031-.029zM8.02 15.331c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.956-2.418 2.157-2.418 1.21 0 2.176 1.094 2.157 2.418 0 1.334-.956 2.419-2.157 2.419zm7.975 0c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.955-2.418 2.157-2.418 1.21 0 2.176 1.094 2.157 2.419 0 1.334-.946 2.419-2.157 2.419z"/></svg>
        </div>
        <div class="min-w-0">
            <div class="flex items-center gap-2">
                <h3 class="text-lg font-semibold text-zp-ink">Discord</h3>
                <span class="rounded-full border border-zp-border px-2 py-0.5 text-[10px] font-medium text-zp-muted">Coming soon</span>
            </div>
            <p class="mt-1 text-sm text-zp-desc">A real-time chat community is on the way. Until then, GitHub Discussions is the best place to connect. Watch the repository or check the roadmap for launch updates.</p>
        </div>
    </div>

    <!-- Contributors -->
    <div class="mt-16">
        <h2 class="text-xs font-semibold uppercase tracking-widest text-zp-link">Contributors</h2>
        <p class="mt-3 text-sm text-zp-desc max-w-2xl">ZeroPing is shaped by people who file issues, improve docs, and send pull requests. Every contribution — large or small — moves the framework forward.</p>

        <div class="mt-6 grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="rounded-2xl border border-zp-border bg-zp-surface p-5 flex items-center gap-4">
                <div class="flex items-center justify-center h-12 w-12 rounded-full bg-gradient-to-br from-zp-primary to-cyan-400 text-zp-ink font-bold text-lg">RN</div>
                <div>
                    <p class="text-sm font-semibold text-zp-ink">Rin Nairith</p>
                    <p class="text-xs text-zp-desc">Creator &amp; Maintainer</p>
                </div>
            </div>
            <div class="rounded-2xl border border-dashed border-zp-border bg-zp-surface/40 p-5 flex items-center gap-4">
                <div class="flex items-center justify-center h-12 w-12 rounded-full bg-zp-surface border border-zp-border text-zp-muted">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-zp-ink">You?</p>
                    <p class="text-xs text-zp-desc">Become a contributor</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Sponsors -->
    <div class="mt-16">
        <h2 class="text-xs font-semibold uppercase tracking-widest text-zp-link">Sponsors</h2>
        <p class="mt-3 text-sm text-zp-desc max-w-2xl">ZeroPing is free, open-source software. Sponsorship funds infrastructure, documentation, and time spent maintaining the framework.</p>

        <div class="mt-6 rounded-2xl border border-dashed border-zp-border bg-zp-surface/40 p-10 text-center">
            <svg class="h-9 w-9 mx-auto text-zp-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/></svg>
            <p class="mt-4 text-sm text-zp-desc">Sponsorship options will be announced alongside the community launch. Until then, the best way to support ZeroPing is to build with it and spread the word.</p>
            <div class="mt-6"><?php render_component('button', ['label' => 'Star on GitHub', 'href' => $repositoryUrl ?? '#']); ?></div>
        </div>
    </div>

    <!-- Call to action -->
    <div class="mt-16 rounded-3xl border border-zp-border bg-zp-surface overflow-hidden p-10 sm:p-14 text-center">
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[40rem] h-[20rem] bg-zp-link/5 blur-3xl"></div>
        <div class="relative">
            <h2 class="font-display text-3xl sm:text-4xl font-bold text-zp-ink tracking-tight">Ready to contribute?</h2>
            <p class="mt-3 text-zp-desc max-w-xl mx-auto">Read the contribution guide, pick up a good first issue, and open your first pull request.</p>
            <div class="mt-8 flex flex-wrap justify-center gap-3">
                <?php render_component('button', ['label' => 'Read the Docs', 'href' => '/docs/introduction']); ?>
                <?php render_component('button', ['label' => 'View Repository', 'href' => $repositoryUrl ?? '#', 'variant' => 'secondary']); ?>
            </div>
        </div>
    </div>
</section>
