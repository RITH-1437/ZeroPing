<?php require_once __DIR__ . '/../components/component.php'; ?>
<section class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
    <?php render_component('breadcrumb', ['items' => [['label' => 'Home', 'href' => '/'], ['label' => 'Roadmap']]]); ?>
    <h1 class="mt-6 text-4xl font-bold tracking-tight">Roadmap</h1>
    <p class="mt-4 text-slate-600 dark:text-slate-300">Our short and mid-term direction for the framework ecosystem.</p>
    <div class="mt-8 space-y-4">
        <?php render_component('alert', ['type' => 'success', 'message' => 'v1.0 shipped core routing, ORM, queue, cache, mail, and security features.']); ?>
        <?php render_component('alert', ['type' => 'info', 'message' => 'v1.1: Documentation search indexing and first-party starter templates.']); ?>
        <?php render_component('alert', ['type' => 'warning', 'message' => 'v1.2: Official package registry and cloud deployment recipes.']); ?>
    </div>
</section>
