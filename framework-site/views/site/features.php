<?php require_once __DIR__ . '/../components/component.php'; ?>
<section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8" data-animate>
    <?php render_component('breadcrumb', ['items' => [['label' => 'Home', 'href' => '/'], ['label' => 'Features']]]); ?>
    <h1 class="mt-6 font-display text-4xl sm:text-5xl font-bold tracking-tight text-zp-ink">Framework Features</h1>
    <p class="mt-4 max-w-3xl text-zp-desc">ZeroPing ships with the modules you need to move from idea to production with a clean, predictable developer workflow.</p>

    <?php
    $groups = [
        'Core Architecture' => [
            ['icon' => '<img src="/assets/images/router.png" alt="" class="h-8 w-8 object-contain">', 'title' => 'Routing', 'description' => 'Expressive static and dynamic routing with middleware, groups, prefixes, and named routes.'],
            ['icon' => '<img src="/assets/images/mvc.png" alt="" class="h-8 w-8 object-contain">', 'title' => 'MVC', 'description' => 'A clean Model-View-Controller separation with convention-based controllers and layouts.'],
            ['icon' => '<img src="/assets/images/container.png" alt="" class="h-8 w-8 object-contain">', 'title' => 'Dependency Injection', 'description' => 'A lightweight service container with auto-resolution and singleton binding.'],
            ['icon' => '<img src="/assets/images/middleware.png" alt="" class="h-8 w-8 object-contain">', 'title' => 'Middleware', 'description' => 'A request/response pipeline for authentication, CSRF, rate limiting, and custom logic.'],
        ],
        'Database & ORM' => [
            ['icon' => '<img src="/assets/images/service.png" alt="" class="h-8 w-8 object-contain">', 'title' => 'ORM', 'description' => 'Active-record models with relationships, accessors, mutators, pagination, and soft deletes.'],
            ['icon' => '<img src="/assets/images/migration.png" alt="" class="h-8 w-8 object-contain">', 'title' => 'Migrations', 'description' => 'Version-controlled schema changes with up/down methods and fresh/rollback commands.'],
            ['icon' => '<img src="/assets/images/authentication.png" alt="" class="h-8 w-8 object-contain">', 'title' => 'Authentication', 'description' => 'Session-based auth with password hashing, guards, and a built-in user provider interface.'],
            ['icon' => '<img src="/assets/images/authentication.png" alt="" class="h-8 w-8 object-contain">', 'title' => 'Validation', 'description' => 'Fluent and rule-based validators with form requests and clear error messages.'],
        ],
        'Developer Experience' => [
            ['icon' => '<img src="/assets/images/cli.png" alt="" class="h-8 w-8 object-contain">', 'title' => 'CLI Tooling', 'description' => 'Scaffold, migrate, test, and manage your app from a single Zero CLI surface.'],
            ['icon' => '<img src="/assets/images/queue.png" alt="" class="h-8 w-8 object-contain">', 'title' => 'Queue', 'description' => 'Async job dispatching with sync and database drivers for deferred task processing.'],
            ['icon' => '<img src="/assets/images/debug.png" alt="" class="h-8 w-8 object-contain">', 'title' => 'Testing', 'description' => 'Feature and unit testing tools with HTTP assertions and database transactions built in.'],
            ['icon' => '<img src="/assets/images/mail.png" alt="" class="h-8 w-8 object-contain">', 'title' => 'Mail', 'description' => 'A mail facade with pluggable drivers, mailable classes, and template rendering.'],
        ],
        'Performance & Security' => [
            ['icon' => '<img src="/assets/images/cache.png" alt="" class="h-8 w-8 object-contain">', 'title' => 'Caching', 'description' => 'File, array, and Redis-compatible caches with route and view cache for fast responses.'],
            ['icon' => '<img src="/assets/images/session.png" alt="" class="h-8 w-8 object-contain">', 'title' => 'Sessions', 'description' => 'File, cookie, and database-backed sessions with flash messaging out of the box.'],
            ['icon' => '<img src="/assets/images/authentication.png" alt="" class="h-8 w-8 object-contain">', 'title' => 'Security', 'description' => 'CSRF tokens, signed URLs, hashing utilities, and configurable rate limiting by default.'],
        ],
    ];
    $groupIdx = 0;
    foreach ($groups as $groupName => $features): $groupIdx++; ?>
        <div class="mt-16 <?= $groupIdx > 1 ? 'pt-16 border-t border-zp-border' : '' ?>">
            <h2 class="text-xs font-semibold uppercase tracking-widest text-zp-link"><?= htmlspecialchars($groupName, ENT_QUOTES, 'UTF-8') ?></h2>
            <div class="mt-5 grid md:grid-cols-2 lg:grid-cols-3 gap-5">
                <?php foreach ($features as $feature) {
                    render_component('feature-card', $feature);
                } ?>
            </div>
        </div>
    <?php endforeach; ?>
</section>
