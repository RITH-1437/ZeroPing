<?php
$title = $title ?? 'Build Modern PHP Applications Without the Complexity';
$subtitle = $subtitle ?? 'A developer-focused PHP framework with clean architecture, batteries-included CLI, and premium DX.';
?>
<section class="relative overflow-hidden" data-animate>
    <div class="absolute inset-0 -z-10">
        <div class="absolute -top-32 -left-32 w-96 h-96 bg-blue-500/10 dark:bg-blue-400/5 rounded-full blur-3xl animate-float"></div>
        <div class="absolute -bottom-32 -right-32 w-[30rem] h-[30rem] bg-emerald-500/10 dark:bg-emerald-400/5 rounded-full blur-3xl animate-float-slow" style="animation-delay: -4s;"></div>
        <div class="absolute top-1/3 left-1/2 -translate-x-1/2 -translate-y-1/3 w-[40rem] h-[40rem] bg-indigo-500/8 dark:bg-indigo-400/4 rounded-full blur-3xl animate-float" style="animation-delay: -2s;"></div>
    </div>
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[60rem] h-[30rem] bg-gradient-to-b from-blue-500/5 via-indigo-500/5 to-transparent dark:from-blue-500/10 dark:via-indigo-500/10 blur-3xl -z-10"></div>
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-24 sm:py-32 lg:py-36">
        <div class="lg:grid lg:grid-cols-2 lg:gap-12 lg:items-center">
            <div class="max-w-3xl">
                <?php render_component('badge', ['label' => 'ZeroPing Framework v1.0.0']); ?>
                <h1 class="mt-6 text-4xl sm:text-5xl lg:text-6xl xl:text-7xl font-extrabold tracking-tight leading-[1.08] text-slate-900 dark:text-slate-50">
                    <?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>
                </h1>
                <p class="mt-6 text-base sm:text-lg lg:text-xl text-slate-600 dark:text-slate-400 leading-relaxed max-w-2xl"><?= htmlspecialchars($subtitle, ENT_QUOTES, 'UTF-8') ?></p>
                <div class="mt-10 flex flex-wrap gap-3">
                    <?php render_component('button', ['label' => 'Get Started', 'href' => '/installation']); ?>
                    <?php render_component('button', ['label' => 'Documentation', 'href' => '/docs/introduction', 'variant' => 'secondary']); ?>
                </div>
            </div>
            <div class="hidden lg:block mt-12 lg:mt-0">
                <div class="rounded-2xl border border-slate-700/50 overflow-hidden bg-slate-950 shadow-2xl shadow-blue-500/10 dark:shadow-blue-500/5 animate-fade-in" style="animation-delay: 0.3s;">
                    <div class="flex items-center justify-between px-4 py-2.5 border-b border-slate-800 bg-slate-900/80">
                        <div class="flex items-center gap-2" aria-hidden="true">
                            <span class="h-2.5 w-2.5 rounded-full bg-red-500/80"></span>
                            <span class="h-2.5 w-2.5 rounded-full bg-yellow-500/80"></span>
                            <span class="h-2.5 w-2.5 rounded-full bg-emerald-500/80"></span>
                        </div>
                        <p class="text-xs text-slate-400">quickstart.php</p>
                        <div class="w-14"></div>
                    </div>
                    <pre class="p-4 overflow-x-auto text-sm leading-relaxed"><code class="language-php"><?= htmlspecialchars("<?php\n\nuse App\\Core\\Routing\\Router;\nuse App\\Controllers\\WebsiteController;\n\nRouter::get('/', [WebsiteController::class, 'home']);\nRouter::get('/docs/{slug}', [WebsiteController::class, 'docs']);\n\n// Start the server\nphp public/index.php\n// → http://localhost:1437") ?></code></pre>
                </div>
            </div>
        </div>
    </div>
</section>