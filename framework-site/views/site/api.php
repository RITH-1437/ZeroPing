<?php require_once __DIR__ . '/../components/component.php'; ?>
<section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8" data-animate>
    <?php render_component('breadcrumb', ['items' => [['label' => 'Home', 'href' => '/'], ['label' => 'API']]]); ?>
    <h1 class="mt-6 text-4xl sm:text-5xl font-extrabold tracking-tight text-zp-ink">API Reference</h1>
    <p class="mt-4 text-zp-desc max-w-3xl">A concise tour of the core classes, methods, and namespaces that power everyday ZeroPing development. Browse the building blocks below, then jump into the code examples to see them working together in real applications.</p>

    <!-- Namespaces -->
    <div class="mt-14">
        <h2 class="text-xs font-semibold uppercase tracking-widest text-zp-link">Namespaces</h2>
        <p class="mt-3 text-sm text-zp-desc max-w-3xl">ZeroPing organizes code under the <code class="px-1.5 py-0.5 rounded bg-zp-surface border border-zp-border text-zp-ink">App\Core</code> root namespace, with framework utilities nested by responsibility. Import any of these to extend the framework or build your own packages.</p>
        <div class="mt-5 grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php
            $namespaces = [
                ['ns' => 'App\\Core\\Routing', 'desc' => 'Router, Route, and request dispatching.'],
                ['ns' => 'App\\Core\\Http', 'desc' => 'Request, Response, middleware, and sessions.'],
                ['ns' => 'App\\Core\\Database', 'desc' => 'ORM models, migrations, and the query builder.'],
                ['ns' => 'App\\Core\\Auth', 'desc' => 'Authentication, guards, and password hashing.'],
                ['ns' => 'App\\Core\\Validation', 'desc' => 'Validator, rules, and form requests.'],
                ['ns' => 'App\\Core\\Console', 'desc' => 'CLI commands and the Zero command surface.'],
                ['ns' => 'App\\Core\\Queue', 'desc' => 'Job dispatching and worker consumers.'],
                ['ns' => 'App\\Core\\Mail', 'desc' => 'Mailables and transport drivers.'],
                ['ns' => 'App\\Core\\Cache', 'desc' => 'Cache stores, rate limiting, and views.'],
            ];
            foreach ($namespaces as $n): ?>
                <div class="group rounded-2xl border border-zp-border bg-zp-surface/50 p-5 hover:bg-zp-surface hover:border-cyan-500/20 transition-all duration-300">
                    <code class="text-sm font-mono text-zp-link group-hover:text-cyan-400 transition-colors"><?= htmlspecialchars($n['ns'], ENT_QUOTES, 'UTF-8') ?></code>
                    <p class="mt-2 text-sm text-zp-desc leading-relaxed"><?= htmlspecialchars($n['desc'], ENT_QUOTES, 'UTF-8') ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Classes & Methods -->
    <div class="mt-16 pt-16 border-t border-zp-border">
        <h2 class="text-xs font-semibold uppercase tracking-widest text-zp-link">Classes &amp; Methods</h2>
        <p class="mt-3 text-sm text-zp-desc max-w-3xl">The most frequently used classes and their key methods. Each signature is shown exactly as you would call it in your application.</p>
        <div class="mt-5 grid gap-4">
            <?php
            $classes = [
                [
                    'name' => 'Router',
                    'namespace' => 'App\\Core\\Routing',
                    'desc' => 'Registers and resolves application routes, including method verbs, named routes, prefixes, and middleware groups.',
                    'methods' => [
                        ['sig' => 'Router::get(string $uri, $handler)', 'desc' => 'Register a GET route.'],
                        ['sig' => 'Router::post(string $uri, $handler)', 'desc' => 'Register a POST route.'],
                        ['sig' => 'Router::prefix(string $prefix, Closure $group)', 'desc' => 'Group routes under a URI prefix.'],
                        ['sig' => 'Router::middleware($mw, Closure $group)', 'desc' => 'Apply middleware to a route group.'],
                        ['sig' => 'route(string $name, array $params = [])', 'desc' => 'Generate a URL for a named route.'],
                    ],
                ],
                [
                    'name' => 'Model (ORM base)',
                    'namespace' => 'App\\Core\\Database',
                    'desc' => 'Active-record base class for database entities, with fluent queries, relationships, and soft deletes.',
                    'methods' => [
                        ['sig' => 'Model::create(array $attributes)', 'desc' => 'Insert and return a new record.'],
                        ['sig' => 'Model::find(mixed $id)', 'desc' => 'Find a record by primary key.'],
                        ['sig' => 'Model::where(string $col, $op, $val)', 'desc' => 'Start a fluent query builder.'],
                        ['sig' => 'Model::paginate(int $perPage)', 'desc' => 'Return a paginated result set.'],
                        ['sig' => '$model->save()', 'desc' => 'Persist changes to the database.'],
                    ],
                ],
                [
                    'name' => 'AuthManager',
                    'namespace' => 'App\\Core\\Auth',
                    'desc' => 'Stateless and session-based authentication helpers for logging users in and out.',
                    'methods' => [
                        ['sig' => 'AuthManager::check()', 'desc' => 'Determine if a user is authenticated.'],
                        ['sig' => 'AuthManager::user()', 'desc' => 'Retrieve the authenticated user.'],
                        ['sig' => 'AuthManager::login(Model $user)', 'desc' => 'Log a user into the session.'],
                        ['sig' => 'AuthManager::logout()', 'desc' => 'Clear the authenticated session.'],
                    ],
                ],
                [
                    'name' => 'Validator',
                    'namespace' => 'App\\Core\\Validation',
                    'desc' => 'Validates input against rule strings or arrays, then returns only the safe data.',
                    'methods' => [
                        ['sig' => 'Validator::make(array $data, array $rules)', 'desc' => 'Create a validator instance.'],
                        ['sig' => '$v->fails()', 'desc' => 'Return true if validation failed.'],
                        ['sig' => '$v->validated()', 'desc' => 'Get the validated data only.'],
                    ],
                ],
                [
                    'name' => 'Cache',
                    'namespace' => 'App\\Core\\Cache',
                    'desc' => 'Key-value caching with multiple stores, TTL support, and remember helpers.',
                    'methods' => [
                        ['sig' => 'Cache::get(string $key, $default)', 'desc' => 'Retrieve a cached value.'],
                        ['sig' => 'Cache::put(string $key, $value, $ttl)', 'desc' => 'Store a value with TTL.'],
                        ['sig' => 'Cache::remember(string $key, $ttl, Closure $cb)', 'desc' => 'Get or compute-and-store a value.'],
                    ],
                ],
            ];
            foreach ($classes as $c): ?>
                <div class="rounded-2xl border border-zp-border bg-zp-surface/50 overflow-hidden">
                    <div class="flex flex-wrap items-center gap-2 px-5 py-4 border-b border-zp-border bg-zp-surface">
                        <code class="text-sm font-mono font-semibold text-zp-ink"><?= htmlspecialchars($c['name'], ENT_QUOTES, 'UTF-8') ?></code>
                        <span class="text-xs text-zp-muted">in</span>
                        <code class="text-xs font-mono text-zp-link"><?= htmlspecialchars($c['namespace'], ENT_QUOTES, 'UTF-8') ?></code>
                        <span class="ml-auto text-[11px] font-medium rounded-full px-2 py-0.5 bg-emerald-500/10 text-emerald-400 border border-emerald-500/20"><?= count($c['methods']) ?> methods</span>
                    </div>
                    <div class="px-5 py-4">
                        <p class="text-sm text-zp-desc mb-4"><?= htmlspecialchars($c['desc'], ENT_QUOTES, 'UTF-8') ?></p>
                        <div class="grid md:grid-cols-2 gap-x-8 gap-y-3">
                            <?php foreach ($c['methods'] as $m): ?>
                                <div class="flex flex-col">
                                    <code class="text-[13px] font-mono text-zp-ink bg-zp-bg border border-zp-border rounded-lg px-2.5 py-1.5 overflow-x-auto"><?= htmlspecialchars($m['sig'], ENT_QUOTES, 'UTF-8') ?></code>
                                    <span class="mt-1.5 text-xs text-zp-desc leading-relaxed"><?= htmlspecialchars($m['desc'], ENT_QUOTES, 'UTF-8') ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Code examples -->
    <div class="mt-16 pt-16 border-t border-zp-border">
        <h2 class="text-xs font-semibold uppercase tracking-widest text-zp-link">Code Examples</h2>
        <p class="mt-3 text-sm text-zp-desc max-w-3xl">Copy-ready snippets for the most common API surfaces, grouped by the task you are solving.</p>
        <?php
        $groups = [
            'Routing' => [
                'desc' => 'Map URLs to controllers and generate URLs for named routes.',
                'items' => [
                ['title' => 'Register Routes', 'code' => "Router::get('/', [Controller::class, 'method']);\nRouter::post('/login', [Controller::class, 'method']);\nRouter::prefix('/admin', fn() => {\n    Router::get('/dashboard', ...);\n});"],
                ['title' => 'Named Routes', 'code' => "Router::get('/posts/{id}', [PostController::class, 'show'])\n    ->name('posts.show');\n\n// Generate URL\nroute('posts.show', ['id' => 1]);"],
                ],
            ],
            'Validation' => [
                'desc' => 'Validate user input and retrieve only the safe fields.',
                'items' => [
                ['title' => 'Validate Input', 'code' => "\$validator = Validator::make(\$data, [\n    'email' => 'required|email',\n    'password' => 'required|min:8',\n]);\n\nif (\$validator->fails()) {\n    // handle errors\n}"],
                ],
            ],
            'Database & ORM' => [
                'desc' => 'Persist and query records with the active-record model.',
                'items' => [
                ['title' => 'Create a Model', 'code' => "\$user = User::create([\n    'first_name' => 'Rin',\n    'last_name' => 'Nairith',\n    'email' => 'nairithrin143@gmail.com',\n    'password' => PasswordHasher::make('secret123'),\n]);"],
                ['title' => 'Query Records', 'code' => "\$users = User::where('role', 'admin')\n    ->orderBy('created_at', 'desc')\n    ->paginate(15);\n\n\$user = User::find(\$id);"],
                ],
            ],
            'Security' => [
                'desc' => 'Authenticate users and protect forms from CSRF forgery.',
                'items' => [
                ['title' => 'Authentication', 'code' => "if (AuthManager::check()) {\n    \$user = AuthManager::user();\n}\n\nAuthManager::login(\$user);\nAuthManager::logout();"],
                ['title' => 'CSRF Protection', 'code' => "// In view\n<?= csrf_field() ?>\n\n// Validate\nif (!SessionGuard::validateCsrf(\$token)) {\n    // reject\n}"],
                ],
            ],
            'CLI Commands' => [
                'desc' => 'Common Zero CLI commands for day-to-day development.',
                'items' => [
                ['title' => 'Built-in Commands', 'code' => "php zero serve           # Start dev server\nphp zero migrate         # Run migrations\nphp zero make:controller # Create controller\nphp zero route:list      # List all routes\nphp zero cache:clear     # Clear cache"],
                ],
            ],
        ];
        foreach ($groups as $groupName => $group): ?>
            <div class="mt-8">
                <h3 class="text-sm font-semibold text-zp-ink"><?= htmlspecialchars($groupName, ENT_QUOTES, 'UTF-8') ?></h3>
                <p class="mt-1 text-xs text-zp-desc"><?= htmlspecialchars($group['desc'], ENT_QUOTES, 'UTF-8') ?></p>
                <div class="mt-3 grid md:grid-cols-2 gap-4">
                    <?php foreach ($group['items'] as $item): ?>
                        <div class="rounded-2xl border border-zp-border bg-zp-surface p-5 shadow-sm hover:shadow-md transition-shadow duration-200">
                            <h4 class="text-sm font-semibold text-zp-ink mb-3"><?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') ?></h4>
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
    </div>
</section>
