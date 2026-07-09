<?php require_once __DIR__ . '/../components/component.php'; ?>
<section class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8" data-animate>
    <?php render_component('breadcrumb', ['items' => [['label' => 'Home', 'href' => '/'], ['label' => 'Roadmap']]]); ?>
    <h1 class="mt-6 text-4xl sm:text-5xl font-extrabold tracking-tight text-slate-900 dark:text-slate-50">Roadmap</h1>
    <p class="mt-4 text-slate-600 dark:text-slate-400">Our short and mid-term direction for the framework ecosystem.</p>

    <?php
    $milestones = [
        [
            'version' => 'v1.0',
            'status' => 'completed',
            'date' => 'Released',
            'items' => [
                'Core routing system with middleware support',
                'Active-record ORM with relationships',
                'Queue system with database driver',
                'Cache system with file and array drivers',
                'Mail system with pluggable drivers',
                'Security features (CSRF, signed URLs, hashing)',
            ],
        ],
        [
            'version' => 'v1.1',
            'status' => 'in-progress',
            'date' => 'Next release',
            'items' => [
                'Documentation search indexing',
                'First-party starter templates',
                'Enhanced validation rules',
                'Performance optimizations',
            ],
        ],
        [
            'version' => 'v1.2',
            'status' => 'planned',
            'date' => 'Planned',
            'items' => [
                'Official package registry',
                'Cloud deployment recipes',
                'API rate limiting enhancements',
                'Webhook system',
            ],
        ],
        [
            'version' => 'v2.0',
            'status' => 'planned',
            'date' => 'Future',
            'items' => [
                'Full async event system',
                'Real-time broadcasting',
                'First-party billing module',
                'Official documentation overhaul',
            ],
        ],
    ];

    $statusConfig = [
        'completed' => [
            'border' => 'border-emerald-500/40',
            'bg' => 'bg-emerald-50/80 dark:bg-emerald-950/40',
            'text' => 'text-emerald-700 dark:text-emerald-300',
            'dot' => 'bg-emerald-500 shadow-emerald-500/30',
            'badge' => 'Completed',
            'icon' => 'check',
        ],
        'in-progress' => [
            'border' => 'border-blue-500/40',
            'bg' => 'bg-blue-50/80 dark:bg-blue-950/40',
            'text' => 'text-blue-700 dark:text-blue-300',
            'dot' => 'bg-blue-500 shadow-blue-500/30',
            'badge' => 'In Progress',
            'icon' => 'progress',
        ],
        'planned' => [
            'border' => 'border-slate-300/60 dark:border-slate-700/60',
            'bg' => 'bg-slate-100/80 dark:bg-slate-800/60',
            'text' => 'text-slate-600 dark:text-slate-400',
            'dot' => 'bg-slate-400 dark:bg-slate-500',
            'badge' => 'Planned',
            'icon' => 'planned',
        ],
    ];
    ?>

    <div class="mt-10">
        <?php foreach ($milestones as $i => $milestone):
            $cfg = $statusConfig[$milestone['status']];
        ?>
            <div class="relative flex gap-5 pb-8 <?= $i < count($milestones) - 1 ? '' : 'pb-0' ?>" data-animate>
                <div class="flex flex-col items-center">
                    <div class="flex items-center justify-center h-10 w-10 rounded-xl <?= $cfg['bg'] ?> border <?= $cfg['border'] ?> shadow-sm">
                        <?php if ($cfg['icon'] === 'check'): ?>
                            <svg class="h-5 w-5 <?= $cfg['text'] ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        <?php elseif ($cfg['icon'] === 'progress'): ?>
                            <svg class="h-5 w-5 <?= $cfg['text'] ?> animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182"/></svg>
                        <?php else: ?>
                            <svg class="h-5 w-5 <?= $cfg['text'] ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <?php endif; ?>
                    </div>
                    <?php if ($i < count($milestones) - 1): ?>
                        <div class="flex-1 w-px bg-gradient-to-b from-slate-300 to-transparent dark:from-slate-700"></div>
                    <?php endif; ?>
                </div>
                <div class="flex-1 min-w-0 pb-2">
                    <div class="rounded-2xl border border-slate-200/70 dark:border-slate-800/70 bg-white/80 dark:bg-slate-900/80 p-6 shadow-sm">
                        <div class="flex flex-wrap items-center gap-3 mb-4">
                            <h2 class="text-xl font-bold text-slate-900 dark:text-slate-100"><?= htmlspecialchars($milestone['version'], ENT_QUOTES, 'UTF-8') ?></h2>
                            <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-[10px] font-semibold uppercase tracking-wider <?= $cfg['border'] ?> <?= $cfg['bg'] ?> <?= $cfg['text'] ?>"><?= $cfg['badge'] ?></span>
                            <span class="text-xs text-slate-400 dark:text-slate-500"><?= htmlspecialchars($milestone['date'], ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                        <ul class="space-y-2" data-animate-stagger>
                            <?php foreach ($milestone['items'] as $item): ?>
                                <li class="flex items-start gap-2.5 text-sm text-slate-600 dark:text-slate-400">
                                    <svg class="h-4 w-4 shrink-0 mt-0.5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    <?= htmlspecialchars($item, ENT_QUOTES, 'UTF-8') ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>