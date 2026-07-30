<?php require_once __DIR__ . '/../components/component.php'; ?>
<section class="mx-auto flex min-h-[62vh] max-w-5xl items-center px-4 py-16 sm:px-6 lg:px-8">
    <div class="w-full overflow-hidden rounded-3xl border border-zp-border bg-zp-surface/75 p-8 text-center shadow-sm sm:p-14">
        <p class="font-mono text-sm font-bold tracking-[.2em] text-zp-link">404</p><h1 class="mt-4 font-display text-4xl font-bold tracking-[-.05em] text-zp-ink sm:text-5xl">This route went missing.</h1><p class="mx-auto mt-4 max-w-lg text-base leading-7 text-zp-desc">The page may have moved, the URL may be incomplete, or this feature is not available yet. Let’s get you back to something useful.</p>
        <div class="mt-8 flex flex-wrap justify-center gap-3"><?php render_component('button', ['label' => 'Go home', 'href' => '/']); ?><?php render_component('button', ['label' => 'Search documentation', 'href' => '/docs', 'variant' => 'secondary']); ?></div>
        <div class="mx-auto mt-10 grid max-w-2xl gap-3 border-t border-zp-border pt-7 sm:grid-cols-3"><a href="/getting-started" class="rounded-xl bg-zp-bg p-3 text-sm font-semibold text-zp-desc hover:text-zp-link focus-ring">Getting started</a><a href="/docs/introduction" class="rounded-xl bg-zp-bg p-3 text-sm font-semibold text-zp-desc hover:text-zp-link focus-ring">Documentation</a><a href="/community" class="rounded-xl bg-zp-bg p-3 text-sm font-semibold text-zp-desc hover:text-zp-link focus-ring">Community</a></div>
    </div>
</section>
