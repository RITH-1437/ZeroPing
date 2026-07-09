<div class="flex items-center justify-between mb-8">
    <h1 class="text-3xl font-bold">Users</h1>
    <a href="/users/create" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
        Create User
    </a>
</div>

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
            <?php foreach ($users as $user): ?>
                <tr class="border-t border-slate-200 dark:border-slate-700">
                    <td class="px-4 py-3"><?= e($user->id) ?></td>
                    <td class="px-4 py-3"><?= e($user->name) ?></td>
                    <td class="px-4 py-3"><?= e($user->email) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
