<?php require_once __DIR__ . '/../components/component.php'; ?>
<section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
    <?php render_component('breadcrumb', ['items' => [['label' => 'Home', 'href' => '/'], ['label' => 'Getting Started']]]); ?>
    <h1 class="mt-6 text-4xl sm:text-5xl font-extrabold tracking-tight text-zp-ink">Getting Started</h1>
    <p class="mt-4 text-zp-desc">Create routes, controllers, and views using a clear MVC structure.</p>

    <div class="mt-10 space-y-0">
        <?php
        $flow = [
            ['icon' => '/assets/images/router.png', 'label' => '1. Define a Route', 'filename' => 'config/routes.php', 'code' => "use App\\Core\\Routing\\Router;\nuse App\\Controllers\\WebsiteController;\n\nRouter::get('/', [WebsiteController::class, 'home']);"],
            ['icon' => '/assets/images/controller.png', 'label' => '2. Create a Controller', 'filename' => 'App/Controllers/HomeController.php', 'code' => "public function home(): void\n{\n    \$this->view('site/home', [\n        'title' => 'ZeroPing',\n        'active' => 'home',\n    ]);\n}"],
            ['icon' => '/assets/images/view.png', 'label' => '3. Build a View', 'filename' => 'resources/views/site/home.php', 'code' => "<!-- views/site/home.php -->\n<h1>Welcome to ZeroPing</h1>\n<p>Your app is running.</p>"],
            ['icon' => '/assets/images/run.png', 'label' => '4. Run the App', 'filename' => 'terminal', 'language' => 'bash', 'code' => "php public\\index.php\n# Server started on http://localhost:1437"],
        ];
        foreach ($flow as $i => $step): ?>
            <div class="relative">
                <div class="flex items-start gap-4">
                    <div class="hidden sm:flex flex-col items-center">
                        <div class="flex items-center justify-center h-10 w-10 rounded-xl bg-zp-primary/10 ring-1 ring-zp-primary/20 shrink-0" aria-hidden="true">
                            <img src="<?= $step['icon'] ?>" alt="" class="h-5 w-5 object-contain">
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm font-semibold text-zp-ink mb-2"><?= htmlspecialchars($step['label'], ENT_QUOTES, 'UTF-8') ?></h3>
                        <?php render_component('code-block', [
                            'title' => $step['filename'],
                            'language' => $step['language'] ?? 'PHP',
                            'codeId' => 'gs-flow-' . $i,
                            'code' => $step['code'],
                            'width' => ($step['filename'] === 'terminal') ? 'half' : '',
                        ]); ?>
                    </div>
                </div>
                <?php if ($i < count($flow) - 1): ?>
                    <div class="hidden sm:flex justify-center py-3 text-zp-desc">
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