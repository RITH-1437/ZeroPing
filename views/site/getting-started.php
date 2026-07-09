<?php require_once __DIR__ . '/../components/component.php'; ?>
<section class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8" data-animate>
    <?php render_component('breadcrumb', ['items' => [['label' => 'Home', 'href' => '/'], ['label' => 'Getting Started']]]); ?>
    <h1 class="mt-6 text-4xl sm:text-5xl font-extrabold tracking-tight text-slate-900 dark:text-slate-50">Getting Started</h1>
    <p class="mt-4 text-slate-600 dark:text-slate-400">Create routes, controllers, and views using a clear MVC structure.</p>

    <div class="mt-10 space-y-0">
        <?php
        $flow = [
            ['icon' => '/assets/images/router.png', 'title' => '1. Define a Route', 'code' => "use App\\Core\\Routing\\Router;\nuse App\\Controllers\\WebsiteController;\n\nRouter::get('/', [WebsiteController::class, 'home']);"],
            ['icon' => '/assets/images/controller.png', 'title' => '2. Create a Controller', 'code' => "public function home(): void\n{\n    \$this->view('site/home', [\n        'title' => 'ZeroPing',\n        'active' => 'home',\n    ]);\n}"],
            ['icon' => '/assets/images/view.png', 'title' => '3. Build a View', 'code' => "<!-- views/site/home.php -->\n<h1>Welcome to ZeroPing</h1>\n<p>Your app is running.</p>"],
            ['icon' => '/assets/images/run.png', 'title' => '4. Run the App', 'code' => "php public\\index.php\n# Server started on http://localhost:1437"],
        ];
        foreach ($flow as $i => $step): ?>
            <div class="relative">
                <div class="flex items-start gap-4">
                    <div class="hidden sm:flex flex-col items-center">
                        <div class="flex items-center justify-center h-10 w-10 rounded-xl bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-950/60 dark:to-indigo-950/60 ring-1 ring-blue-200/50 dark:ring-blue-800/50 shrink-0" aria-hidden="true">
                            <img src="<?= $step['icon'] ?>" alt="" class="h-5 w-5 object-contain">
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <?php render_component('code-block', [
                            'title' => $step['title'],
                            'language' => 'PHP',
                            'codeId' => 'gs-flow-' . $i,
                            'code' => $step['code'],
                        ]); ?>
                    </div>
                </div>
                <?php if ($i < count($flow) - 1): ?>
                    <div class="hidden sm:flex justify-center py-3 text-slate-300 dark:text-slate-600">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m19 9-7 7-7-7"/></svg>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="mt-10 flex flex-wrap gap-3">
        <?php render_component('button', ['label' => 'Open API Reference', 'href' => '/api']); ?>
        <?php render_component('button', ['label' => 'Read Documentation', 'href' => '/docs/introduction', 'variant' => 'secondary']); ?>
    </div>
</section>