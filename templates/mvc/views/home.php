<div class="py-8">
    <div class="text-center mb-10">
        <div class="w-16 h-16 rounded-2xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"/></svg>
        </div>
        <h1 class="text-3xl font-bold mb-2"><?= e(config('app.name')) ?></h1>
        <p class="text-slate-500 dark:text-slate-400 max-w-md mx-auto">Get your MVC application up and running in three simple steps.</p>
    </div>

    <div class="max-w-2xl mx-auto space-y-4">
        <div class="relative pl-14 <?= $migrated ? 'opacity-60' : '' ?>">
            <div class="absolute left-0 top-0 w-10 h-10 rounded-xl flex items-center justify-center text-sm font-bold
                <?= $migrated ? 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400' : 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 ring-2 ring-blue-500/30' ?>">
                <?php if ($migrated) : ?>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                <?php else : ?>
                    1
                <?php endif; ?>
            </div>
            <div class="p-5 rounded-xl border <?= $migrated ? 'border-green-200 dark:border-green-900/40 bg-green-50 dark:bg-green-900/10' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800' ?>">
                <h3 class="font-semibold mb-1">Configure Database</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-3">Run the migration to create the <code class="font-mono text-xs px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-700">users</code> table.</p>
                <?php if ($migrated) : ?>
                    <span class="inline-flex items-center gap-1.5 text-xs font-medium text-green-600 dark:text-green-400">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        Migration complete
                    </span>
                <?php else : ?>
                    <code class="inline-block px-3 py-1.5 rounded-lg bg-slate-900 dark:bg-slate-700 text-green-400 text-sm font-mono">php zero migrate</code>
                <?php endif; ?>
            </div>
        </div>

        <div class="relative pl-14 <?= !$migrated ? 'opacity-30 pointer-events-none' : '' ?>">
            <div class="absolute left-0 top-0 w-10 h-10 rounded-xl flex items-center justify-center text-sm font-bold
                <?= $userCount > 0 ? 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400' : ($migrated ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 ring-2 ring-blue-500/30' : 'bg-slate-100 dark:bg-slate-800 text-slate-400') ?>">
                <?php if ($userCount > 0) : ?>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                <?php else : ?>
                    2
                <?php endif; ?>
            </div>
            <div class="p-5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800">
                <h3 class="font-semibold mb-1">Manage Users</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-3">Create, view, and manage users through the CRUD interface.</p>
                <?php if ($userCount > 0) : ?>
                    <span class="text-xs text-slate-500 dark:text-slate-400"><?= $userCount ?> user<?= $userCount !== 1 ? 's' : '' ?> created</span>
                <?php endif; ?>
                <div class="flex gap-2 mt-3">
                    <a href="/users" class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"/></svg>
                        View Users
                    </a>
                    <a href="/users/create" class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium rounded-lg border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 hover:border-blue-500 hover:text-blue-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Create User
                    </a>
                </div>
            </div>
        </div>

        <div class="relative pl-14 <?= !$migrated ? 'opacity-30 pointer-events-none' : '' ?>">
            <div class="absolute left-0 top-0 w-10 h-10 rounded-xl flex items-center justify-center text-sm font-bold bg-slate-100 dark:bg-slate-800 text-slate-400">
                3
            </div>
            <div class="p-5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800">
                <h3 class="font-semibold mb-1">Build Your App</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400">Extend the scaffold &mdash; add models, controllers, and views for your own entities.</p>
                <div class="mt-3 flex flex-wrap gap-2">
                    <code class="text-xs px-2 py-1 rounded bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 font-mono">php zero make:model Product</code>
                    <code class="text-xs px-2 py-1 rounded bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 font-mono">php zero make:controller ProductController</code>
                </div>
            </div>
        </div>
    </div>
</div>
