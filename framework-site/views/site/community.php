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
                    <img src="/assets/images/discussion.png" alt="" class="h-6 w-6 object-contain">
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
                    <img src="/assets/images/issue.png" alt="" class="h-6 w-6 object-contain">
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
            <img src="/assets/images/discord.png" alt="" class="h-6 w-6 object-contain">
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
                <div class="flex items-center justify-center h-12 w-12 rounded-full bg-zp-surface border border-zp-border overflow-hidden">
                    <img src="/assets/images/me.png" alt="Rin Nairith" class="h-12 w-12 object-cover">
                </div>
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
            <img src="/assets/images/sponser.png" alt="" class="h-9 w-9 mx-auto object-contain">
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
