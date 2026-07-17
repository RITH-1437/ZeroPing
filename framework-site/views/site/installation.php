<?php require_once __DIR__ . '/../components/component.php'; ?>
<section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
    <?php render_component('breadcrumb', ['items' => [['label' => 'Home', 'href' => '/'], ['label' => 'Installation']]]); ?>
    <h1 class="mt-6 text-4xl sm:text-5xl font-extrabold tracking-tight text-zp-ink">Installation</h1>
    <p class="mt-4 text-zp-desc">Install ZeroPing and run your first app locally in a few commands.</p>

    <div class="mt-6 rounded-2xl border border-cyan-200/60 dark:border-cyan-900/60 bg-cyan-50/80 dark:bg-cyan-950/50 p-4 flex items-start gap-3 text-sm text-cyan-800 dark:text-cyan-200" role="status">
        <svg class="h-5 w-5 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span>Requirements: <strong>PHP 8.1+</strong>, <strong>Composer</strong>, and a <strong>MySQL-compatible database</strong>.</span>
    </div>

    <!-- Method 1: Zero CLI -->
    <div class="mt-10" data-animate>
        <div class="flex items-center gap-3 mb-4">
            <h2 class="text-2xl font-bold text-zp-ink">Method 1 — Zero CLI</h2>
            <span class="rounded-full px-3 py-0.5 text-xs font-semibold bg-emerald-500/10 text-emerald-600 border border-emerald-500/20">Recommended</span>
        </div>
        <p class="text-zp-desc mb-4">Creates a new ZeroPing application instantly using the Zero CLI.</p>

        <?php render_component('code-block', [
            'title' => 'terminal',
            'language' => 'bash',
            'codeId' => 'install-cli',
            'width' => 'half',
            'code' => "php zero new my-app\ncd my-app\nphp zero serve",
        ]); ?>
    </div>

    <!-- Method 2: Composer -->
    <div class="mt-10" data-animate>
        <div class="flex items-center gap-3 mb-4">
            <h2 class="text-2xl font-bold text-zp-ink">Method 2 — Composer</h2>
        </div>
        <p class="text-zp-desc mb-4">Installs ZeroPing directly from Packagist using Composer.</p>

        <?php render_component('code-block', [
            'title' => 'terminal',
            'language' => 'bash',
            'codeId' => 'install-composer',
            'width' => 'half',
            'code' => "composer create-project rith-1437/zeroping my-app\ncd my-app\nphp zero serve",
        ]); ?>
    </div>

    <div class="mt-6 rounded-2xl border border-zp-border bg-zp-surface/50 p-4 flex items-start gap-3 text-sm text-zp-desc" role="status">
        <svg class="h-5 w-5 shrink-0 mt-0.5 text-zp-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span>Both methods generate the exact same project structure. Use <strong>Zero CLI</strong> for the fastest experience, or <strong>Composer</strong> if it's already installed on your system.</span>
    </div>

    <div class="mt-10 p-6 rounded-2xl border border-emerald-200/60 dark:border-emerald-900/60 bg-emerald-50/80 dark:bg-emerald-950/50 flex items-start gap-3 text-sm text-emerald-800 dark:text-emerald-200" role="status">
        <svg class="h-5 w-5 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <div>
            <strong>Ready to go!</strong> Your ZeroPing application is now running.
            <div class="mt-3"><?php render_component('button', ['label' => 'Continue to Getting Started', 'href' => '/getting-started']); ?></div>
        </div>
    </div>
</section>
