<h1 class="text-3xl font-bold mb-8">Create User</h1>

<form method="POST" action="/users" class="max-w-md space-y-4">
    <div>
        <label for="name" class="block text-sm font-medium mb-1">Name</label>
        <input type="text" name="name" id="name" required
            class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800">
    </div>
    <div>
        <label for="email" class="block text-sm font-medium mb-1">Email</label>
        <input type="email" name="email" id="email" required
            class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800">
    </div>
    <div>
        <label for="password" class="block text-sm font-medium mb-1">Password</label>
        <input type="password" name="password" id="password" required
            class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800">
    </div>
    <button type="submit"
        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
        Create
    </button>
    <a href="/users" class="ml-2 text-sm text-slate-500 hover:text-slate-700">Cancel</a>
</form>
