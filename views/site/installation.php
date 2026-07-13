<?php require_once __DIR__ . '/../components/component.php'; ?>
<section class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8" data-animate>
    <?php render_component('breadcrumb', ['items' => [['label' => 'Home', 'href' => '/'], ['label' => 'Installation']]]); ?>
    <h1 class="mt-6 text-4xl sm:text-5xl font-extrabold tracking-tight text-zp-white">Installation</h1>
    <p class="mt-4 text-zp-muted">Install ZeroPing and run your first app locally in a few commands.</p>

    <div class="mt-6 rounded-2xl border border-blue-200/60 dark:border-blue-900/60 bg-blue-50/80 dark:bg-blue-950/50 p-4 flex items-start gap-3 text-sm text-blue-800 dark:text-blue-200" role="status">
        <svg class="h-5 w-5 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span>Requirements: <strong>PHP 8.1+</strong>, <strong>Composer</strong>, and a <strong>MySQL-compatible database</strong>.</span>
    </div>

    <?php
    $steps = [
        ['num' => '01', 'label' => 'Clone Repository', 'icon' => 'download', 'code' => 'git clone https://github.com/RITH-1437/ZeroPing.git'],
        ['num' => '02', 'label' => 'Install Dependencies', 'icon' => 'package', 'code' => 'cd ZeroPing' . "\n" . 'composer install'],
        ['num' => '03', 'label' => 'Configure Environment', 'icon' => 'settings', 'code' => 'cp .env.example .env'],
        ['num' => '04', 'label' => 'Run Migrations', 'icon' => 'database', 'code' => 'php cli\\migrate.php'],
    ];
    ?>
    <div class="mt-10">
        <?php foreach ($steps as $i => $step): ?>
            <div class="relative flex gap-5 pb-8 <?= $i < count($steps) - 1 ? '' : 'pb-0' ?>" data-animate>
                <div class="flex flex-col items-center">
                    <div class="flex items-center justify-center h-10 w-10 rounded-xl bg-gradient-to-br from-cyan-500 to-emerald-500 text-zp-white shadow-md shadow-cyan-500/20">
                        <?php if ($step['icon'] === 'download'): ?>
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                        <?php elseif ($step['icon'] === 'package'): ?>
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0l-3-3m3 3l3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/></svg>
                        <?php elseif ($step['icon'] === 'settings'): ?>
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <?php elseif ($step['icon'] === 'database'): ?>
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75C20.25 16.153 16.85 18 12.25 18s-8.25-1.847-8.25-4.125v-3.75m16.5 0c0 2.278-3.4 4.125-8.25 4.125s-8.25-1.847-8.25-4.125"/></svg>
                        <?php endif; ?>
                    </div>
                    <?php if ($i < count($steps) - 1): ?>
                        <div class="flex-1 w-px bg-gradient-to-b from-cyan-500/30 to-transparent dark:from-cyan-400/20 mt-2"></div>
                    <?php endif; ?>
                </div>
                <div class="flex-1 min-w-0 pb-2">
                    <h3 class="text-sm font-semibold text-zp-white mb-2"><?= htmlspecialchars($step['label'], ENT_QUOTES, 'UTF-8') ?></h3>
                    <?php render_component('code-block', [
                        'title' => $step['label'],
                        'codeId' => 'install-step-' . $i,
                        'code' => $step['code'],
                    ]); ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="mt-10 p-6 rounded-2xl border border-emerald-200/60 dark:border-emerald-900/60 bg-emerald-50/80 dark:bg-emerald-950/50 flex items-start gap-3 text-sm text-emerald-800 dark:text-emerald-200" role="status">
        <svg class="h-5 w-5 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <div>
            <strong>Ready to go!</strong> Your ZeroPing application is now running.
            <div class="mt-3"><?php render_component('button', ['label' => 'Continue to Getting Started', 'href' => '/getting-started']); ?></div>
        </div>
    </div>
</section>