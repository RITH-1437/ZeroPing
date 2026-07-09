<?php require_once __DIR__ . '/../components/component.php'; ?>
<section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8" data-animate>
    <?php render_component('breadcrumb', ['items' => [['label' => 'Home', 'href' => '/'], ['label' => 'Features']]]); ?>
    <h1 class="mt-6 text-4xl sm:text-5xl font-extrabold tracking-tight text-slate-900 dark:text-slate-50">Framework Features</h1>
    <p class="mt-4 max-w-3xl text-slate-600 dark:text-slate-400">ZeroPing includes the modules you need to move from idea to production with a clean, predictable developer workflow.</p>

    <?php
    $groups = [
        'Core' => [
            ['icon' => '<img src="/assets/images/router.png" alt="" class="h-8 w-8 object-contain">', 'title' => 'Routing', 'description' => 'Simple static + dynamic routing with middleware support, route groups, prefixes, and named routes.'],
            ['icon' => '<img src="/assets/images/container.png" alt="" class="h-8 w-8 object-contain">', 'title' => 'Container', 'description' => 'Dependency injection via a lightweight service container with auto-resolution and singleton support.'],
            ['icon' => '<img src="/assets/images/middleware.png" alt="" class="h-8 w-8 object-contain">', 'title' => 'Middleware', 'description' => 'Request/response middleware pipeline for authentication, CSRF, rate limiting, and custom logic.'],
        ],
        'Database & ORM' => [
            ['icon' => '<img src="/assets/images/service.png" alt="" class="h-8 w-8 object-contain">', 'title' => 'ORM', 'description' => 'Active-record style model layer with relationships, soft deletes, timestamps, and eager loading.'],
            ['icon' => '<img src="/assets/images/migration.png" alt="" class="h-8 w-8 object-contain">', 'title' => 'Migrations', 'description' => 'Version-controlled database schema migrations with up/down methods and fresh/rollback commands.'],
            ['icon' => '<img src="/assets/images/authentication.png" alt="" class="h-8 w-8 object-contain">', 'title' => 'Authentication', 'description' => 'Session-based auth with password hashing, guards, and a built-in user provider interface.'],
        ],
        'Developer Experience' => [
            ['icon' => '<img src="/assets/images/cli.png" alt="" class="h-8 w-8 object-contain">', 'title' => 'CLI Tooling', 'description' => 'Make commands, migrations, cache operations, route listing, and testing from a single CLI surface.'],
            ['icon' => '<img src="/assets/images/queue.png" alt="" class="h-8 w-8 object-contain">', 'title' => 'Queue', 'description' => 'Async job dispatching with sync and database drivers for deferred task processing.'],
            ['icon' => '<img src="/assets/images/debug.png" alt="" class="h-8 w-8 object-contain">', 'title' => 'Debug Layer', 'description' => 'Debug bar and collectors for requests, routes, SQL queries, and memory usage profiling.'],
        ],
        'Security & Communication' => [
            ['icon' => '<img src="/assets/images/authentication.png" alt="" class="h-8 w-8 object-contain">', 'title' => 'Security', 'description' => 'CSRF tokens, signed URLs, hash utilities, and configurable rate limiting out of the box.'],
            ['icon' => '<img src="/assets/images/mail.png" alt="" class="h-8 w-8 object-contain">', 'title' => 'Mail', 'description' => 'Mail facade with pluggable drivers, mailable classes, and template rendering for transactional emails.'],
            ['icon' => '<img src="/assets/images/testing.png" alt="" class="h-8 w-8 object-contain">', 'title' => 'Testing', 'description' => 'Feature and unit testing tools built directly into the framework for reliable test coverage.'],
        ],
    ];
    $groupIdx = 0;
    foreach ($groups as $groupName => $features): $groupIdx++; ?>
        <div class="mt-16 <?= $groupIdx > 1 ? 'pt-16 border-t border-slate-200/60 dark:border-slate-800/60' : '' ?>">
            <h2 class="text-xs font-semibold uppercase tracking-widest text-blue-600 dark:text-blue-400"><?= htmlspecialchars($groupName, ENT_QUOTES, 'UTF-8') ?></h2>
            <div class="mt-5 grid md:grid-cols-2 lg:grid-cols-3 gap-5">
                <?php foreach ($features as $feature) {
                    render_component('feature-card', $feature);
                } ?>
            </div>
        </div>
    <?php endforeach; ?>
</section>