<?php require_once __DIR__ . '/../components/component.php'; ?>
<section class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
    <?php render_component('breadcrumb', ['items' => [['label' => 'Home', 'href' => '/'], ['label' => 'Getting Started']]]); ?>
    <h1 class="mt-6 text-4xl font-bold tracking-tight">Getting Started</h1>
    <p class="mt-4 text-slate-600 dark:text-slate-300">Create routes, controllers, and views using a clear MVC structure.</p>
    <div class="mt-6"><?php render_component('code-block', ['title' => 'Your first route', 'codeId' => 'gs-route', 'code' => "use App\\Core\\Routing\\Router;\nuse App\\Controllers\\WebsiteController;\n\nRouter::get('/', [WebsiteController::class, 'home']);"]); ?></div>
    <div class="mt-6"><?php render_component('code-block', ['title' => 'Render a view', 'codeId' => 'gs-controller', 'code' => "public function home(): void\n{\n    \$this->view('site/home', ['title' => 'ZeroPing']);\n}"]); ?></div>
    <div class="mt-6 flex gap-3">
        <?php render_component('button', ['label' => 'Open API Reference', 'href' => '/api']); ?>
        <?php render_component('button', ['label' => 'Read Documentation', 'href' => '/docs/introduction', 'variant' => 'secondary']); ?>
    </div>
</section>
