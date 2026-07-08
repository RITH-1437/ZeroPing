<?php require_once __DIR__ . '/../components/component.php'; ?>
<section class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
    <?php render_component('breadcrumb', ['items' => [['label' => 'Home', 'href' => '/'], ['label' => 'API']]]); ?>
    <h1 class="mt-6 text-4xl font-bold tracking-tight">API Overview</h1>
    <p class="mt-4 text-slate-600 dark:text-slate-300">Core APIs that power daily framework development.</p>
    <div class="mt-8 grid lg:grid-cols-2 gap-6">
        <?php render_component('code-block', ['title' => 'Router API', 'codeId' => 'api-router', 'code' => "Router::get('/', [WebsiteController::class, 'home']);\nRouter::get('/features', [WebsiteController::class, 'features']);\nRouter::get('/docs/{slug}', [WebsiteController::class, 'docs']);\nRouter::get('/api', [WebsiteController::class, 'api']);"]); ?>
        <?php render_component('code-block', ['title' => 'Model API', 'codeId' => 'api-model', 'code' => "\$user = User::create([\n    'first_name' => 'Rin',\n    'last_name' => 'Nairith',\n    'email' => 'nairithrin143@gmail.com',\n    'password' => PasswordHasher::make('secret123'),\n]);"]); ?>
        <?php render_component('code-block', ['title' => 'Validation API', 'codeId' => 'api-validation', 'code' => "\$validator = Validator::make(\$data, [\n    'email' => 'required|email',\n    'password' => 'required|min:6',\n]);\n\nif (\$validator->fails()) {\n    return;\n}"]); ?>
        <?php render_component('code-block', ['title' => 'Response API', 'codeId' => 'api-response', 'code' => "Response::json(['ok' => true]);\nResponse::redirect('/docs/introduction');"]); ?>
    </div>
</section>
