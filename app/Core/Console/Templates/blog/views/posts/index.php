<h1 class="text-3xl font-bold mb-8">Blog</h1>

<div class="grid gap-6">
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
            <time class="text-xs text-slate-400 mt-2 block">
                <?= $post->published_at ? date('M j, Y', strtotime($post->published_at)) : '' ?>
            </time>
        </article>
    <?php endforeach; ?>
</div>

<?php if (function_exists('paginate_links')): ?>
    <div class="mt-8"><?= paginate_links($posts) ?></div>
<?php endif; ?>
