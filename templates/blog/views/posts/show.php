<article class="prose dark:prose-invert max-w-none">
    <h1><?= e($post->title) ?></h1>
    <?php if ($post->published_at) : ?>
        <p class="text-slate-500 dark:text-slate-400 text-sm">
            Published on <?= date('F j, Y', strtotime($post->published_at)) ?>
        </p>
    <?php endif; ?>
    <div class="mt-8">
        <?= $post->content ?>
    </div>
</article>

<a href="/blog" class="inline-flex items-center gap-1 mt-8 text-blue-600 dark:text-blue-400 hover:underline">
    &larr; Back to Blog
</a>
