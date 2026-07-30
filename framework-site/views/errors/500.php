<?php require_once __DIR__ . '/../components/component.php'; ?>
<section class="mx-auto flex min-h-[62vh] max-w-5xl items-center px-4 py-16 sm:px-6 lg:px-8">
    <div class="w-full overflow-hidden rounded-3xl border border-zp-border bg-zp-surface/75 p-8 text-center shadow-sm sm:p-14">
        <p class="font-mono text-sm font-bold tracking-[.2em] text-amber-500">500</p><h1 class="mt-4 font-display text-4xl font-bold tracking-[-.05em] text-zp-ink sm:text-5xl">The server hit an unexpected turn.</h1><p class="mx-auto mt-4 max-w-lg text-base leading-7 text-zp-desc">Nothing you need to fix. Try again in a moment, or use the links below to continue exploring ZeroPing while we recover.</p>
        <div class="mt-8 flex flex-wrap justify-center gap-3"><?php render_component('button', ['label' => 'Return home', 'href' => '/']); ?><?php render_component('button', ['label' => 'System status', 'href' => '/up', 'variant' => 'secondary']); ?></div>
        <p class="mt-8 text-xs text-zp-muted">If this keeps happening, please include the page URL when reporting it on GitHub.</p>
    </div>
</section>
