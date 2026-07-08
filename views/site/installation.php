<?php require_once __DIR__ . '/../components/component.php'; ?>
<section class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
    <?php render_component('breadcrumb', ['items' => [['label' => 'Home', 'href' => '/'], ['label' => 'Installation']]]); ?>
    <h1 class="mt-6 text-4xl font-bold tracking-tight">Installation</h1>
    <p class="mt-4 text-slate-600 dark:text-slate-300">Install ZeroPing and run your first app locally in a few commands.</p>
    <div class="mt-6"><?php render_component('alert', ['type' => 'info', 'message' => 'Requirements: PHP 8.1+, Composer, and a MySQL-compatible database.']); ?></div>
    <div class="mt-6"><?php render_component('code-block', ['title' => 'Install ZeroPing', 'codeId' => 'install-code', 'code' => "git clone https://github.com/RITH-1437/ZeroPing.git\ncd ZeroPing\ncomposer install\ncp .env.example .env\nphp cli\\migrate.php"]); ?></div>
    <div class="mt-6"><?php render_component('button', ['label' => 'Continue to Getting Started', 'href' => '/getting-started']); ?></div>
</section>
