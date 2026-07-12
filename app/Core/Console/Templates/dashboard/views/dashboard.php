<div class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-bold">Dashboard</h1>
        <p class="text-slate-500 dark:text-slate-400">Welcome to your <?= e(config('app.name')) ?> admin panel.</p>
    </div>
    <a href="/users/create" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
        Add User
    </a>
</div>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-10">
    <div class="bg-white dark:bg-slate-800 rounded-xl p-6 border border-slate-200 dark:border-slate-700">
        <p class="text-sm text-slate-500 dark:text-slate-400">Total Users</p>
        <p class="text-3xl font-bold mt-2"><?= e($totalUsers) ?></p>
    </div>
    <div class="bg-white dark:bg-slate-800 rounded-xl p-6 border border-slate-200 dark:border-slate-700">
        <p class="text-sm text-slate-500 dark:text-slate-400">Framework</p>
        <p class="text-3xl font-bold mt-2">ZeroPing</p>
    </div>
    <div class="bg-white dark:bg-slate-800 rounded-xl p-6 border border-slate-200 dark:border-slate-700">
        <p class="text-sm text-slate-500 dark:text-slate-400">PHP Version</p>
        <p class="text-3xl font-bold mt-2"><?= e(PHP_VERSION) ?></p>
    </div>
</div>

<h2 class="text-xl font-semibold mb-4">Recent Users</h2>
<div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-700">
    <table class="w-full text-sm">
        <thead class="bg-slate-100 dark:bg-slate-800">
            <tr>
                <th class="px-4 py-3 text-left font-medium">ID</th>
                <th class="px-4 py-3 text-left font-medium">Name</th>
                <th class="px-4 py-3 text-left font-medium">Email</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($recentUsers as $user): ?>
                <tr class="border-t border-slate-200 dark:border-slate-700">
                    <td class="px-4 py-3"><?= e($user->id) ?></td>
                    <td class="px-4 py-3"><?= e($user->name) ?></td>
                    <td class="px-4 py-3"><?= e($user->email) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
