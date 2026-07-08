<?php
$title = $title ?? 'Build modern web apps with ZeroPing';
$subtitle = $subtitle ?? 'A developer-focused PHP framework with clean architecture, batteries included CLI, and premium DX.';
?>
<section class="fade-up relative overflow-hidden">
    <div class="absolute inset-0 -z-10 bg-[radial-gradient(circle_at_top,rgba(37,99,235,0.18),transparent_45%),radial-gradient(circle_at_70%_20%,rgba(16,185,129,0.16),transparent_35%)]"></div>
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-16 sm:py-20 lg:py-24">
        <div class="max-w-3xl">
            <?php render_component('badge', ['label' => 'Official Website v1.0.0']); ?>
            <h1 class="mt-6 text-4xl sm:text-5xl lg:text-6xl font-bold tracking-tight text-slate-900 dark:text-slate-50"><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></h1>
            <p class="mt-6 text-base sm:text-lg text-slate-700 dark:text-slate-300 leading-relaxed"><?= htmlspecialchars($subtitle, ENT_QUOTES, 'UTF-8') ?></p>
            <div class="mt-8 flex flex-wrap gap-3">
                <?php render_component('button', ['label' => '🚀 Get Started', 'href' => '/installation']); ?>
                <?php render_component('button', ['label' => '📖 Documentation', 'href' => '/docs', 'variant' => 'secondary']); ?>
            </div>
        </div>
    </div>
</section>
