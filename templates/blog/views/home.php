<div class="py-8">
    <div class="text-center mb-10">
        <div class="w-16 h-16 rounded-2xl bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
        </div>
        <h1 class="text-3xl font-bold mb-2"><?= e(config('app.name')) ?></h1>
        <p class="text-slate-500 dark:text-slate-400 max-w-md mx-auto">Set up your blog and start publishing in no time.</p>
    </div>

    <div class="max-w-2xl mx-auto space-y-4">
        <div class="relative pl-14 <?= $migrated ? 'opacity-60' : '' ?>">
            <div class="absolute left-0 top-0 w-10 h-10 rounded-xl flex items-center justify-center text-sm font-bold
                <?= $migrated ? 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400' : 'bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 ring-2 ring-amber-500/30' ?>">
                <?php if ($migrated) : ?>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                <?php else : ?>
                    1
                <?php endif; ?>
            </div>
            <div class="p-5 rounded-xl border <?= $migrated ? 'border-green-200 dark:border-green-900/40 bg-green-50 dark:bg-green-900/10' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800' ?>">
                <h3 class="font-semibold mb-1">Run Migrations</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-3">Create the <code class="font-mono text-xs px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-700">posts</code> table in your database.</p>
                <?php if ($migrated) : ?>
                    <span class="inline-flex items-center gap-1.5 text-xs font-medium text-green-600 dark:text-green-400">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        Migration complete
                    </span>
                <?php else : ?>
                    <code class="inline-block px-3 py-1.5 rounded-lg bg-slate-900 dark:bg-slate-700 text-amber-400 text-sm font-mono">php zero migrate</code>
                <?php endif; ?>
            </div>
        </div>

        <div class="relative pl-14 <?= !$migrated ? 'opacity-30 pointer-events-none' : '' ?>">
            <div class="absolute left-0 top-0 w-10 h-10 rounded-xl flex items-center justify-center text-sm font-bold
                <?= $postCount > 0 ? 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400' : ($migrated ? 'bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 ring-2 ring-amber-500/30' : 'bg-slate-100 dark:bg-slate-800 text-slate-400') ?>">
                <?php if ($postCount > 0) : ?>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                <?php else : ?>
                    2
                <?php endif; ?>
            </div>
            <div class="p-5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800">
                <h3 class="font-semibold mb-1">Write Your First Post</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-3">Add content to your blog through migrations, seeders, or direct database inserts.</p>
                <div class="flex flex-wrap gap-2">
                    <a href="/blog" class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium rounded-lg bg-amber-600 text-white hover:bg-amber-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                        View Blog
                    </a>
                    <?php if (!$migrated || $postCount === 0) : ?>
                        <code class="inline-flex items-center px-3 py-2 text-xs rounded-lg bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 font-mono">php zero make:seeder PostSeeder</code>
                    <?php endif; ?>
                </div>
                <?php if ($postCount > 0) : ?>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-3"><?= $postCount ?> post<?= $postCount !== 1 ? 's' : '' ?> published</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="relative pl-14 <?= !$migrated ? 'opacity-30 pointer-events-none' : '' ?>">
            <div class="absolute left-0 top-0 w-10 h-10 rounded-xl flex items-center justify-center text-sm font-bold bg-slate-100 dark:bg-slate-800 text-slate-400">
                3
            </div>
            <div class="p-5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800">
                <h3 class="font-semibold mb-1">Customize Your Blog</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400">Extend the blog with categories, tags, comments, or a custom theme.</p>
                <div class="mt-3 flex flex-wrap gap-2">
                    <code class="text-xs px-2 py-1 rounded bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 font-mono">php zero make:model Category</code>
                    <code class="text-xs px-2 py-1 rounded bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 font-mono">php zero make:controller CommentController</code>
                </div>
            </div>
        </div>
    </div>

    <?php if ($migrated && $postCount > 0) : ?>
        <div class="max-w-2xl mx-auto mt-10">
            <h3 class="text-sm font-semibold text-slate-500 dark:text-slate-400 mb-4 uppercase tracking-wider">Latest Posts</h3>
            <div class="grid gap-4">
                <?php foreach ($latestPosts as $post) : ?>
                    <a href="/blog/<?= e($post->slug) ?>" class="flex items-center gap-4 p-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:shadow-md transition-shadow group">
                        <div class="w-10 h-10 rounded-lg bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="font-semibold text-sm group-hover:text-amber-600 dark:group-hover:text-amber-400 transition-colors truncate"><?= e($post->title) ?></h4>
                            <?php if ($post->excerpt) : ?>
                                <p class="text-xs text-slate-500 dark:text-slate-400 truncate"><?= e($post->excerpt) ?></p>
                            <?php endif; ?>
                        </div>
                        <svg class="w-5 h-5 text-slate-300 dark:text-slate-600 group-hover:text-amber-500 transition-colors flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>
