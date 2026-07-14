<section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-20 text-center relative" data-animate>
    <div class="absolute inset-0 -z-10 overflow-hidden">
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[30rem] h-[30rem] bg-red-500/10 dark:bg-red-400/5 rounded-full blur-3xl"></div>
    </div>
    <h1 class="text-8xl sm:text-9xl font-extrabold tracking-tight text-transparent bg-clip-text bg-gradient-to-br from-red-600 via-rose-600 to-red-600 dark:from-red-400 dark:via-rose-400 dark:to-red-400">500</h1>
    <h2 class="mt-4 text-2xl sm:text-3xl font-bold text-slate-900 dark:text-slate-100">Something went wrong</h2>

    <?php if (!empty($debug)): ?>
        <div class="mt-8 mx-auto max-w-3xl text-left">
            <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-900 dark:bg-slate-950 overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-800 bg-slate-800/40 flex items-center gap-2">
                    <span class="h-3 w-3 rounded-full bg-red-500"></span>
                    <span class="h-3 w-3 rounded-full bg-amber-400"></span>
                    <span class="h-3 w-3 rounded-full bg-emerald-400"></span>
                    <span class="ml-2 text-xs font-medium text-slate-400">Debug — ZeroPing <?= e(\App\Core\Application\App::VERSION) ?></span>
                </div>
                <div class="px-4 py-4 space-y-3 text-xs leading-relaxed font-mono">
                    <div>
                        <div class="text-rose-400 font-semibold">Message</div>
                        <div class="text-slate-200"><?= e($message ?? 'An unexpected error occurred while handling your request.') ?></div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <div class="text-slate-400">Exception</div>
                            <div class="text-slate-200"><?= e($exception ?: '—') ?></div>
                        </div>
                        <div>
                            <div class="text-slate-400">Location</div>
                            <div class="text-slate-200"><?= e($file ?: '—') ?>:<?= e((string) $line) ?></div>
                        </div>
                        <div>
                            <div class="text-slate-400">Request</div>
                            <div class="text-slate-200"><?= e($requestMethod) ?> <?= e($requestUrl) ?></div>
                        </div>
                        <div>
                            <div class="text-slate-400">Environment</div>
                            <div class="text-slate-200"><?= e($environment) ?></div>
                        </div>
                    </div>
                    <div>
                        <div class="text-slate-400 mb-1">Stack Trace</div>
                        <pre class="overflow-x-auto max-h-72 text-slate-300 bg-black/30 rounded-lg p-3"><code><?php
                            $traceOut = '';
                            foreach (array_slice($trace ?? [], 0, 15) as $frame) {
                                $traceOut .= (isset($frame['file']) ? $frame['file'] : '[internal]')
                                    . ':' . ($frame['line'] ?? '?')
                                    . '  ' . ($frame['class'] ?? '')
                                    . ($frame['type'] ?? '')
                                    . ($frame['function'] ?? '') . "()\n";
                            }
                            echo e($traceOut);
                        ?></code></pre>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <p class="mt-3 text-slate-600 dark:text-slate-400 max-w-md mx-auto">An unexpected error occurred while handling your request.</p>
        <p class="mt-2 text-slate-400 dark:text-slate-500 text-sm">If this persists, check the application logs or contact the site administrator.</p>
    <?php endif; ?>

    <a href="/" class="mt-8 inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-red-600 via-rose-600 to-red-600 bg-[length:200%_200%] px-5 py-2.5 text-sm font-medium text-white shadow-lg shadow-red-600/20 dark:shadow-red-500/10 hover:shadow-xl hover:shadow-red-600/30 dark:hover:shadow-red-500/20 hover:brightness-110 focus-ring transition-all duration-300">Go Back Home</a>
</section>
