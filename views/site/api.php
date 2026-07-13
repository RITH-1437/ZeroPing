<?php require_once __DIR__ . '/../components/component.php'; ?>
<section class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8" data-animate>
    <?php render_component('breadcrumb', ['items' => [['label' => 'Home', 'href' => '/'], ['label' => 'API']]]); ?>
    <h1 class="mt-6 text-4xl sm:text-5xl font-extrabold tracking-tight text-zp-white">API Overview</h1>
    <p class="mt-4 text-zp-muted">Core APIs that power daily framework development. Click any example to expand.</p>

    <?php
    $groups = [
        'Routing' => [
            ['title' => 'Register Routes', 'code' => "Router::get('/', [Controller::class, 'method']);\nRouter::post('/login', [Controller::class, 'method']);\nRouter::prefix('/admin', fn() => {\n    Router::get('/dashboard', ...);\n});"],
            ['title' => 'Named Routes', 'code' => "Router::get('/posts/{id}', [PostController::class, 'show'])\n    ->name('posts.show');\n\n// Generate URL\nroute('posts.show', ['id' => 1]);"],
        ],
        'Validation' => [
            ['title' => 'Validate Input', 'code' => "\$validator = Validator::make(\$data, [\n    'email' => 'required|email',\n    'password' => 'required|min:8',\n]);\n\nif (\$validator->fails()) {\n    // handle errors\n}"],
        ],
        'Database & ORM' => [
            ['title' => 'Create a Model', 'code' => "\$user = User::create([\n    'first_name' => 'Rin',\n    'last_name' => 'Nairith',\n    'email' => 'nairithrin143@gmail.com',\n    'password' => PasswordHasher::make('secret123'),\n]);"],
            ['title' => 'Query Records', 'code' => "\$users = User::where('role', 'admin')\n    ->orderBy('created_at', 'desc')\n    ->paginate(15);\n\n\$user = User::find(\$id);"],
        ],
        'Security' => [
            ['title' => 'Authentication', 'code' => "if (AuthManager::check()) {\n    \$user = AuthManager::user();\n}\n\nAuthManager::login(\$user);\nAuthManager::logout();"],
            ['title' => 'CSRF Protection', 'code' => "// In view\n<?= csrf_field() ?>\n\n// Validate\nif (!SessionGuard::validateCsrf(\$token)) {\n    // reject\n}"],
        ],
        'CLI Commands' => [
            ['title' => 'Built-in Commands', 'code' => "php zero serve           # Start dev server\nphp zero migrate         # Run migrations\nphp zero make:controller # Create controller\nphp zero route:list      # List all routes\nphp zero cache:clear     # Clear cache"],
        ],
    ];
    foreach ($groups as $groupName => $items): ?>
        <div class="mt-14">
            <h2 class="text-xs font-semibold uppercase tracking-widest text-cyan-400"><?= htmlspecialchars($groupName, ENT_QUOTES, 'UTF-8') ?></h2>
            <div class="mt-4 grid md:grid-cols-2 gap-4">
                <?php foreach ($items as $item): ?>
                    <div class="rounded-2xl border border-zp-border bg-zp-surface p-5 shadow-sm hover:shadow-md transition-shadow duration-200">
                        <h3 class="text-sm font-semibold text-zp-white mb-3"><?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') ?></h3>
                        <?php render_component('code-block', [
                            'title' => $item['title'],
                            'language' => 'PHP',
                            'codeId' => 'api-' . md5($item['title']),
                            'code' => $item['code'],
                        ]); ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>
</section>