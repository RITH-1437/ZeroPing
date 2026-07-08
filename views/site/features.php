<?php require_once __DIR__ . '/../components/component.php'; ?>
<section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
    <?php render_component('breadcrumb', ['items' => [['label' => 'Home', 'href' => '/'], ['label' => 'Features']]]); ?>
    <h1 class="mt-6 text-4xl font-bold tracking-tight">Framework Features</h1>
    <p class="mt-4 max-w-3xl text-slate-600 dark:text-slate-300">ZeroPing includes the modules you need to move from idea to production with a clean, predictable developer workflow.</p>

    <div class="mt-10 grid md:grid-cols-2 lg:grid-cols-3 gap-5">
        <?php
        $features = [
            ['icon' => '🧭', 'title' => 'Routing', 'description' => 'Simple static + dynamic routing with middleware support.'],
            ['icon' => '🗄️', 'title' => 'ORM', 'description' => 'Active-record style model layer with relationships and soft deletes.'],
            ['icon' => '📦', 'title' => 'Container', 'description' => 'Dependency injection via a lightweight service container.'],
            ['icon' => '🧪', 'title' => 'Testing', 'description' => 'Feature and unit testing tools built directly into the framework.'],
            ['icon' => '⚡', 'title' => 'Queue', 'description' => 'Async job dispatching with sync and database drivers.'],
            ['icon' => '📮', 'title' => 'Mail', 'description' => 'Mail facade with pluggable drivers and mailable support.'],
            ['icon' => '🧰', 'title' => 'CLI Tooling', 'description' => 'Make commands, migrations, cache operations, and route listing.'],
            ['icon' => '🔐', 'title' => 'Security', 'description' => 'CSRF tools, signed URLs, hash utilities, and rate limiting.'],
            ['icon' => '📊', 'title' => 'Debug Layer', 'description' => 'Debug bar and collectors for requests, routes, SQL, and memory usage.'],
        ];
        foreach ($features as $feature) {
            render_component('feature-card', $feature);
        }
        ?>
    </div>
</section>
