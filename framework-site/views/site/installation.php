<?php require_once __DIR__ . '/../components/component.php'; ?>
<section class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8" data-animate>
    <?php render_component('breadcrumb', ['items' => [['label' => 'Home', 'href' => '/'], ['label' => 'Installation']]]); ?>
    <div class="mt-6 grid items-end gap-8 lg:grid-cols-[1fr_auto]">
        <div><p class="text-xs font-bold uppercase tracking-[.16em] text-zp-link">01 · Installation</p><h1 class="mt-3 font-display text-4xl font-bold tracking-[-.045em] text-zp-ink sm:text-5xl">From zero to running in minutes.</h1><p class="mt-4 max-w-2xl text-lg leading-8 text-zp-desc">Choose the workflow that suits your machine. Both paths create the same clean ZeroPing application.</p></div>
        <div class="rounded-2xl border border-teal-500/20 bg-teal-500/5 px-5 py-4 text-sm text-zp-desc"><span class="block font-semibold text-zp-ink">New project?</span><span class="mt-1 block">The Zero CLI is the quickest route.</span></div>
    </div>

    <div class="mt-10 grid gap-4 sm:grid-cols-3">
        <?php foreach ([['PHP 8.1+', 'The framework runtime'], ['Composer 2', 'Project installation'], ['SQLite included', 'MySQL or Postgres optional']] as [$label, $copy]): ?>
            <div class="rounded-2xl border border-zp-border bg-zp-surface/70 p-5 shadow-sm"><div class="flex h-8 w-8 items-center justify-center rounded-lg bg-teal-500/10 text-sm font-bold text-zp-link" aria-hidden="true">✓</div><h2 class="mt-4 text-sm font-bold text-zp-ink"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></h2><p class="mt-1 text-xs leading-5 text-zp-desc"><?= htmlspecialchars($copy, ENT_QUOTES, 'UTF-8') ?></p></div>
        <?php endforeach; ?>
    </div>

    <div class="mt-12 grid gap-5 lg:grid-cols-2">
        <article class="relative overflow-hidden rounded-3xl border border-teal-500/25 bg-zp-surface p-6 shadow-sm sm:p-8">
            <div class="absolute right-0 top-0 rounded-bl-2xl bg-teal-500/10 px-4 py-2 text-[10px] font-bold uppercase tracking-[.12em] text-zp-link">Recommended</div>
            <p class="text-xs font-bold uppercase tracking-[.14em] text-zp-link">Option A</p><h2 class="mt-3 text-2xl font-bold tracking-[-.03em] text-zp-ink">Zero CLI</h2><p class="mt-2 text-sm leading-6 text-zp-desc">Create a tailored project through the same developer tool you will use every day.</p>
            <?php render_component('code-block', ['title' => 'terminal', 'language' => 'bash', 'codeId' => 'install-cli', 'code' => "php zero new my-app\ncd my-app\nphp zero serve"]); ?>
        </article>
        <article class="rounded-3xl border border-zp-border bg-zp-surface/70 p-6 shadow-sm sm:p-8">
            <p class="text-xs font-bold uppercase tracking-[.14em] text-zp-muted">Option B</p><h2 class="mt-3 text-2xl font-bold tracking-[-.03em] text-zp-ink">Composer</h2><p class="mt-2 text-sm leading-6 text-zp-desc">Start from Packagist when Composer is already part of your standard workflow.</p>
            <?php render_component('code-block', ['title' => 'terminal', 'language' => 'bash', 'codeId' => 'install-composer', 'code' => "composer create-project rith-1437/zeroping my-app\ncd my-app\nphp zero serve"]); ?>
        </article>
    </div>

    <div class="mt-10 rounded-3xl border border-zp-border bg-zp-surface/70 p-6 sm:p-8">
        <div class="flex flex-col justify-between gap-6 md:flex-row md:items-center"><div><p class="text-xs font-bold uppercase tracking-[.14em] text-zp-link">Your first success</p><h2 class="mt-2 text-xl font-bold text-zp-ink">Open <code class="rounded bg-zp-bg px-1.5 py-0.5 text-sm">http://localhost:1437</code></h2><p class="mt-2 max-w-xl text-sm leading-6 text-zp-desc">You should see your local application. From there, create your first route and keep the feedback loop short.</p></div><?php render_component('button', ['label' => 'Continue to Getting Started', 'href' => '/getting-started']); ?></div>
        <ol class="mt-7 grid gap-3 border-t border-zp-border pt-6 sm:grid-cols-3 text-sm"><li class="flex gap-3 text-zp-desc"><span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-teal-500/10 text-xs font-bold text-zp-link">1</span>Project created</li><li class="flex gap-3 text-zp-desc"><span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-teal-500/10 text-xs font-bold text-zp-link">2</span>Server running</li><li class="flex gap-3 text-zp-desc"><span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-teal-500/10 text-xs font-bold text-zp-link">3</span>Ready to build</li></ol>
    </div>
</section>
