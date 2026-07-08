<?php require_once __DIR__ . '/../components/component.php'; ?>
<?php render_component('hero', [
    'title' => 'The official ZeroPing Framework website',
    'subtitle' => 'Ship modern PHP apps with an expressive architecture, practical tooling, and premium developer experience.'
]); ?>

<section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 mt-6">
    <div class="grid lg:grid-cols-3 gap-5">
        <?php render_component('feature-card', ['icon' => '⚙️', 'title' => 'Composable Core', 'description' => 'Routing, ORM, queue, cache, mail, and scheduling designed as clean, focused modules.']); ?>
        <?php render_component('feature-card', ['icon' => '🛠️', 'title' => 'Powerful CLI', 'description' => 'Generate files, run migrations, inspect routes, and test framework features from one command surface.']); ?>
        <?php render_component('feature-card', ['icon' => '🚀', 'title' => 'Production Ready', 'description' => 'Strong defaults for security, performance, and maintainability without sacrificing flexibility.']); ?>
    </div>
</section>

<section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 mt-12 grid lg:grid-cols-2 gap-6">
    <?php render_component('code-block', [
        'title' => 'Code Preview',
        'codeId' => 'home-code-preview',
        'code' => <<<'CODE'
// config/routes.php
Router::get('/', [WebsiteController::class, 'home']);
Router::get('/docs/{slug}', [WebsiteController::class, 'docs']);

// app/Controllers/WebsiteController.php
public function home(): void
{
    $this->view('site/home', ['title' => 'ZeroPing']);
}
CODE
    ]); ?>

    <?php render_component('terminal-window', [
        'title' => 'CLI Preview',
        'lines' => [
            '$ php cli\\migrate.php',
            '✔ Migrated: 001_create_users_table',
            '$ php public\\index.php',
            'ZeroPing server started on http://localhost:1437',
            '$ php zero make:controller DocsController',
            '✔ Controller created successfully.',
        ],
    ]); ?>
</section>

<section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 mt-12 grid lg:grid-cols-2 gap-6">
    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white/80 dark:bg-slate-900/70 p-6">
        <h2 class="text-2xl font-semibold">Installation</h2>
        <p class="mt-3 text-slate-600 dark:text-slate-300">Get started quickly with a clean install flow and minimal setup overhead.</p>
        <?php render_component('code-block', [
            'title' => 'Install in under 3 minutes',
            'codeId' => 'home-install-preview',
            'code' => "git clone https://github.com/RITH-1437/ZeroPing.git\ncd ZeroPing\ncomposer install\ncp .env.example .env",
        ]); ?>
        <div class="mt-4"><?php render_component('button', ['label' => 'Full Installation Guide', 'href' => '/installation']); ?></div>
    </div>

    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white/80 dark:bg-slate-900/70 p-6">
        <h2 class="text-2xl font-semibold">Documentation</h2>
        <p class="mt-3 text-slate-600 dark:text-slate-300">Structured docs with table of contents, copyable snippets, and next/previous navigation.</p>
        <ul class="mt-4 space-y-2 text-slate-700 dark:text-slate-300">
            <li>• Introduction and architecture</li>
            <li>• Installation and setup workflow</li>
            <li>• API references and usage examples</li>
        </ul>
        <div class="mt-4"><?php render_component('button', ['label' => 'Open Docs', 'href' => '/docs/introduction', 'variant' => 'secondary']); ?></div>
    </div>
</section>

<section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 mt-12 grid lg:grid-cols-2 gap-6">
    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white/80 dark:bg-slate-900/70 p-6">
        <h2 class="text-2xl font-semibold">Roadmap</h2>
        <p class="mt-3 text-slate-600 dark:text-slate-300">Track current milestones and upcoming improvements for ZeroPing Framework.</p>
        <ol class="mt-4 space-y-2 text-slate-700 dark:text-slate-300">
            <li>1. Documentation search index and advanced filters.</li>
            <li>2. Package ecosystem for auth, billing, and notifications.</li>
            <li>3. First-party starter kits and deployment templates.</li>
        </ol>
        <div class="mt-4"><?php render_component('button', ['label' => 'View Roadmap', 'href' => '/roadmap']); ?></div>
    </div>

    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white/80 dark:bg-slate-900/70 p-6">
        <h2 class="text-2xl font-semibold">Statistics</h2>
        <div class="mt-4 grid sm:grid-cols-2 gap-4">
            <?php foreach (($stats ?? []) as $item): ?>
                <div class="rounded-xl border border-slate-200 dark:border-slate-700 p-4">
                    <p class="text-2xl font-semibold text-slate-900 dark:text-white"><?= htmlspecialchars($item['value'], ENT_QUOTES, 'UTF-8') ?></p>
                    <p class="mt-1 text-sm text-slate-600 dark:text-slate-400"><?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
