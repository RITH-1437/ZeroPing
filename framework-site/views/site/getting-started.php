<?php require_once __DIR__ . '/../components/component.php'; ?>
<section class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8" data-animate>
    <?php render_component('breadcrumb', ['items' => [['label' => 'Home', 'href' => '/'], ['label' => 'Getting Started']]]); ?>
    <div class="mt-6 grid gap-8 lg:grid-cols-[1fr_220px]">
        <div><p class="text-xs font-bold uppercase tracking-[.16em] text-zp-link">02 · Getting started</p><h1 class="mt-3 font-display text-4xl font-bold tracking-[-.045em] text-zp-ink sm:text-5xl">Build one complete feature.</h1><p class="mt-4 max-w-2xl text-lg leading-8 text-zp-desc">The fastest way to learn ZeroPing is to make a route return a view. This small path shows the framework’s familiar MVC rhythm.</p></div>
        <aside class="rounded-2xl border border-zp-border bg-zp-surface/70 p-5 shadow-sm"><p class="text-xs font-bold uppercase tracking-[.13em] text-zp-muted">What you’ll do</p><ol class="mt-4 space-y-3 text-sm text-zp-desc"><li><span class="mr-2 text-zp-link">01</span>Register a route</li><li><span class="mr-2 text-zp-link">02</span>Write a controller</li><li><span class="mr-2 text-zp-link">03</span>Render a view</li><li><span class="mr-2 text-zp-link">04</span>See it locally</li></ol></aside>
    </div>

    <?php $steps = [
        ['01', 'Register a route', 'Give your application a clear URL and point it at one controller action.', 'config/routes.php', 'PHP', "use App\\Core\\Routing\\Router;\nuse App\\Controllers\\GreetingController;\n\nRouter::get('/hello', [GreetingController::class, 'index']);"],
        ['02', 'Create the controller', 'Controllers keep the request flow explicit and make dependencies easy to find.', 'app/Controllers/GreetingController.php', 'PHP', "<?php\n\nnamespace App\\Controllers;\n\nuse App\\Core\\View\\Controller;\n\nclass GreetingController extends Controller\n{\n    public function index(): string\n    {\n        return view('greeting', ['name' => 'ZeroPing']);\n    }\n}"],
        ['03', 'Render the view', 'Views stay small, escaped by default, and close to the content they serve.', 'views/greeting.php', 'PHP', "<main>\n    <h1>Hello from <?= e(\$name) ?>!</h1>\n</main>"],
        ['04', 'Run and verify', 'Start the local server, then visit the route you just registered.', 'terminal', 'bash', "php zero serve\n\n# Open http://localhost:1437/hello"],
    ]; ?>
    <div class="mt-12 space-y-5">
        <?php foreach ($steps as [$number, $title, $description, $file, $language, $code]): ?>
            <article class="grid gap-5 rounded-3xl border border-zp-border bg-zp-surface/70 p-5 shadow-sm sm:p-7 lg:grid-cols-[78px_1fr]">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-teal-500/10 font-display text-lg font-bold text-zp-link"><?= $number ?></div>
                <div><h2 class="text-xl font-bold tracking-[-.025em] text-zp-ink"><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></h2><p class="mt-2 text-sm leading-6 text-zp-desc"><?= htmlspecialchars($description, ENT_QUOTES, 'UTF-8') ?></p><?php render_component('code-block', ['title' => $file, 'language' => $language, 'codeId' => 'getting-started-' . $number, 'code' => $code]); ?></div>
            </article>
        <?php endforeach; ?>
    </div>
    <div class="mt-10 flex flex-wrap gap-3"><?php render_component('button', ['label' => 'Explore the API', 'href' => '/api']); ?><?php render_component('button', ['label' => 'Browse documentation', 'href' => '/docs/introduction', 'variant' => 'secondary']); ?></div>
</section>
