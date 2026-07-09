<div class="text-center py-12">
    <h1 class="text-4xl font-bold mb-4"><?= e(config('app.name')) ?></h1>
    <p class="text-slate-500 dark:text-slate-400 mb-8">A blog powered by ZeroPing Framework</p>
    <a href="/blog" class="inline-block px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
        Read the Blog
    </a>
</div>

<?php if (!empty($posts)): ?>
    <div class="grid gap-6 mt-12">
        <?php foreach ($posts as $post): ?>
            <article class="p-6 rounded-xl border border-slate-200 dark:border-slate-700 hover:shadow-md transition-shadow">
                <h2 class="text-xl font-semibold mb-2">
                    <a href="/blog/<?= e($post->slug) ?>" class="hover:text-blue-600 dark:hover:text-blue-400">
                        <?= e($post->title) ?>
                    </a>
                </h2>
                <?php if ($post->excerpt): ?>
                    <p class="text-slate-500 dark:text-slate-400"><?= e($post->excerpt) ?></p>
                <?php endif; ?>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
