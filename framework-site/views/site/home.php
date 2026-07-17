<?php require_once __DIR__ . '/../components/component.php'; ?>
<?php render_component('hero'); ?>

<!-- Why ZeroPing -->
<section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-24" data-animate>
  <div class="text-center">
    <h2 class="font-display text-4xl sm:text-5xl font-bold text-zp-ink tracking-tight">Why ZeroPing</h2>
    <p class="mt-4 text-lg text-zp-desc max-w-2xl mx-auto">Built for developers who want simplicity without sacrificing power.</p>
  </div>

  <div class="mt-16 grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
    <?php
    $features = [
      ['icon' => 'zap', 'title' => 'Fast Routing', 'desc' => 'Expressive, regex-powered router with named routes, groups, prefixes, and middleware stacks.'],
      ['icon' => 'database', 'title' => 'Modern ORM', 'desc' => 'Active Record ORM with relationships, accessors, mutators, pagination, and soft deletes.'],
      ['icon' => 'terminal', 'title' => 'Powerful CLI', 'desc' => 'Zero CLI — scaffold, migrate, test, and manage your app from a single command surface.'],
      ['icon' => 'shield', 'title' => 'Secure by Default', 'desc' => 'CSRF protection, encryption, rate limiting, signed URLs, and HTML escaping out of the box.'],
      ['icon' => 'test-tube', 'title' => 'Built-in Testing', 'desc' => 'HTTP assertions, database transactions, test factories, and a dedicated test runner.'],
      ['icon' => 'book-open', 'title' => 'Beautiful Docs', 'desc' => 'Searchable documentation with code examples, dark mode, and interactive navigation.'],
    ];
    foreach ($features as $f): ?>
    <article class="group relative rounded-2xl border border-zp-border bg-zp-surface/50 p-6 hover:bg-zp-surface hover:border-cyan-500/20 hover:shadow-lg hover:shadow-cyan-500/5 transition-all duration-300">
      <div class="absolute inset-0 rounded-2xl bg-zp-primary/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
      <div class="relative">
        <div class="inline-flex items-center justify-center h-11 w-11 rounded-xl bg-zp-surface border border-zp-border text-zp-link transition-all duration-300 group-hover:scale-110 group-hover:border-cyan-500/40 group-hover:shadow-lg group-hover:shadow-cyan-500/10">
          <?php
          $icons = [
            'zap' => '<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"/></svg>',
            'database' => '<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75C20.25 16.153 16.556 18 12 18s-8.25-1.847-8.25-4.125v-3.75m16.5 0c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125"/></svg>',
            'terminal' => '<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 7.5l3 2.25-3 2.25m4.5 0h3m-9 8.25h13.5A2.25 2.25 0 0021 18V6a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 6v12a2.25 2.25 0 002.25 2.25z"/></svg>',
            'shield' => '<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>',
            'test-tube' => '<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23.693L5 14.5m14.8.8l1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.309 48.309 0 0112 21c-2.773 0-5.491-.235-8.135-.687-1.718-.293-2.3-2.379-1.067-3.61L5 14.5"/></svg>',
            'book-open' => '<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>',
          ];
          echo $icons[$f['icon']];
          ?>
        </div>
        <h3 class="mt-5 text-xl font-semibold text-zp-ink group-hover:text-zp-link transition-colors"><?= $f['title'] ?></h3>
        <p class="mt-2 text-sm text-zp-desc leading-relaxed"><?= $f['desc'] ?></p>
      </div>
    </article>
    <?php endforeach; ?>
  </div>
</section>

<!-- Code Showcase -->
<section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-24" data-animate>
  <div class="text-center">
    <h2 class="font-display text-4xl sm:text-5xl font-bold text-zp-ink tracking-tight">Clean. Expressive. Familiar.</h2>
    <p class="mt-4 text-lg text-zp-desc max-w-2xl mx-auto">ZeroPing uses conventions you already know, so you can be productive from the first command.</p>
  </div>

  <div class="mt-16 grid lg:grid-cols-2 gap-6">
    <!-- Left: Routing -->
    <div class="rounded-2xl overflow-hidden shadow-xl" style="border:1px solid #1F3B2D;background:#08120D;">
      <div class="flex items-center justify-between gap-2 px-4 py-3 rounded-t-2xl" style="background:#0E1C15;border-bottom:1px solid #214233;">
        <div class="flex items-center gap-3 min-w-0">
          <div class="flex items-center gap-1.5 shrink-0" aria-hidden="true">
            <span class="h-2.5 w-2.5 rounded-full shadow-sm" style="background:#FF5F56;"></span>
            <span class="h-2.5 w-2.5 rounded-full shadow-sm" style="background:#FFBD2E;"></span>
            <span class="h-2.5 w-2.5 rounded-full shadow-sm" style="background:#27C93F;"></span>
          </div>
          <span class="shrink-0 rounded-full px-2.5 py-0.5 text-[10px] font-semibold uppercase tracking-wider border" style="background:#14532D;color:#BBF7D0;border-color:rgba(34,197,94,0.2);">PHP</span>
          <span class="shrink-0 rounded-md px-2.5 py-0.5 text-[11px] font-medium truncate border" style="background:#1B2A22;color:#D1FAE5;border-color:rgba(42,90,57,0.3);">config/routes.php</span>
        </div>
      </div>
      <div class="p-5 font-mono text-sm leading-relaxed" style="line-height:1.8;font-size:15px;">
        <div style="color:#F8FAFC;"><span style="color:#22C55E;">use</span> <span style="color:#4ADE80;">App\Core\Routing\Router</span>;</div>
        <div style="color:#F8FAFC;"><span style="color:#22C55E;">use</span> <span style="color:#4ADE80;">App\Controllers\HomeController</span>;</div>
        <div style="color:#F8FAFC;">&nbsp;</div>
        <div style="color:#F8FAFC;"><span style="color:#4ADE80;">Router</span>::<span style="color:#22C55E;">get</span>(<span style="color:#FACC15;">'/'</span>, [<span style="color:#86EFAC;">HomeController</span>::<span style="color:#22C55E;">class</span>, <span style="color:#FACC15;">'index'</span>]);</div>
        <div style="color:#F8FAFC;"><span style="color:#4ADE80;">Router</span>::<span style="color:#22C55E;">get</span>(<span style="color:#FACC15;">'/posts/{slug}'</span>, [<span style="color:#86EFAC;">PostController</span>::<span style="color:#22C55E;">class</span>, <span style="color:#FACC15;">'show'</span>]);</div>
        <div style="color:#F8FAFC;"><span style="color:#4ADE80;">Router</span>::<span style="color:#22C55E;">post</span>(<span style="color:#FACC15;">'/posts'</span>, [<span style="color:#86EFAC;">PostController</span>::<span style="color:#22C55E;">class</span>, <span style="color:#FACC15;">'store'</span>]);</div>
        <div style="color:#F8FAFC;">&nbsp;</div>
        <div style="color:#F8FAFC;"><span style="color:#4ADE80;">Router</span>::<span style="color:#22C55E;">group</span>([<span style="color:#FACC15;">'prefix'</span> =&gt; <span style="color:#FACC15;">'admin'</span>], <span style="color:#22C55E;">function</span> () {</div>
        <div style="color:#F8FAFC;">&nbsp;&nbsp;<span style="color:#4ADE80;">Router</span>::<span style="color:#22C55E;">get</span>(<span style="color:#FACC15;">'/dashboard'</span>, [<span style="color:#86EFAC;">AdminController</span>::<span style="color:#22C55E;">class</span>, <span style="color:#FACC15;">'dashboard'</span>]);</div>
        <div style="color:#F8FAFC;">});</div>
      </div>
    </div>

    <!-- Right: Model -->
    <div class="rounded-2xl overflow-hidden shadow-xl" style="border:1px solid #1F3B2D;background:#08120D;">
      <div class="flex items-center justify-between gap-2 px-4 py-3 rounded-t-2xl" style="background:#0E1C15;border-bottom:1px solid #214233;">
        <div class="flex items-center gap-3 min-w-0">
          <div class="flex items-center gap-1.5 shrink-0" aria-hidden="true">
            <span class="h-2.5 w-2.5 rounded-full shadow-sm" style="background:#FF5F56;"></span>
            <span class="h-2.5 w-2.5 rounded-full shadow-sm" style="background:#FFBD2E;"></span>
            <span class="h-2.5 w-2.5 rounded-full shadow-sm" style="background:#27C93F;"></span>
          </div>
          <span class="shrink-0 rounded-full px-2.5 py-0.5 text-[10px] font-semibold uppercase tracking-wider border" style="background:#14532D;color:#BBF7D0;border-color:rgba(34,197,94,0.2);">PHP</span>
          <span class="shrink-0 rounded-md px-2.5 py-0.5 text-[11px] font-medium truncate border" style="background:#1B2A22;color:#D1FAE5;border-color:rgba(42,90,57,0.3);">app/Models/User.php</span>
        </div>
      </div>
      <div class="p-5 font-mono text-sm leading-relaxed" style="line-height:1.8;font-size:15px;">
        <div style="color:#F8FAFC;"><span style="color:#22C55E;">namespace</span> <span style="color:#4ADE80;">App\Models</span>;</div>
        <div style="color:#F8FAFC;">&nbsp;</div>
        <div style="color:#F8FAFC;"><span style="color:#22C55E;">use</span> <span style="color:#4ADE80;">App\Core\Database\Model</span>;</div>
        <div style="color:#F8FAFC;">&nbsp;</div>
        <div style="color:#F8FAFC;"><span style="color:#22C55E;">class</span> <span style="color:#86EFAC;">User</span> <span style="color:#22C55E;">extends</span> <span style="color:#86EFAC;">Model</span></div>
        <div style="color:#F8FAFC;">{</div>
        <div style="color:#F8FAFC;">&nbsp;&nbsp;<span style="color:#22C55E;">public function</span> <span style="color:#4ADE80;">posts</span>(): <span style="color:#86EFAC;">HasMany</span></div>
        <div style="color:#F8FAFC;">&nbsp;&nbsp;{</div>
        <div style="color:#F8FAFC;">&nbsp;&nbsp;&nbsp;&nbsp;<span style="color:#22C55E;">return</span> <span style="color:#22C55E;">$this</span>-><span style="color:#4ADE80;">hasMany</span>(<span style="color:#86EFAC;">Post</span>::<span style="color:#22C55E;">class</span>);</div>
        <div style="color:#F8FAFC;">&nbsp;&nbsp;}</div>
        <div style="color:#F8FAFC;">&nbsp;</div>
        <div style="color:#F8FAFC;">&nbsp;&nbsp;<span style="color:#22C55E;">public function</span> <span style="color:#4ADE80;">scopeActive</span>(<span style="color:#22C55E;">$query</span>)</div>
        <div style="color:#F8FAFC;">&nbsp;&nbsp;{</div>
        <div style="color:#F8FAFC;">&nbsp;&nbsp;&nbsp;&nbsp;<span style="color:#22C55E;">return</span> <span style="color:#22C55E;">$query</span>-><span style="color:#4ADE80;">where</span>(<span style="color:#FACC15;">'active'</span>, <span style="color:#22C55E;">true</span>);</div>
        <div style="color:#F8FAFC;">&nbsp;&nbsp;}</div>
        <div style="color:#F8FAFC;">}</div>
      </div>
    </div>
  </div>
</section>

<!-- Ecosystem -->
<section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-24" data-animate>
  <div class="text-center">
    <h2 class="font-display text-4xl sm:text-5xl font-bold text-zp-ink tracking-tight">Ecosystem</h2>
    <p class="mt-4 text-lg text-zp-desc max-w-2xl mx-auto">A growing ecosystem of tools and resources around ZeroPing.</p>
  </div>

  <div class="mt-16 grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
    <?php
    $ecosystem = [
      ['title' => 'ZeroPing Framework', 'desc' => 'Core framework with MVC, ORM, CLI, and all batteries included.', 'icon' => 'package', 'href' => '/'],
      ['title' => 'ZeroPing Arena', 'desc' => 'Performance benchmarking and optimization playground.', 'icon' => 'bar-chart', 'href' => '/arena', 'disabled' => true],
      ['title' => 'CLI Tooling', 'desc' => 'Scaffolding, migrations, testing, and maintenance from the terminal.', 'icon' => 'terminal', 'href' => '#'],
      ['title' => 'Packages', 'desc' => 'Modular packages extending ZeroPing with queues, mail, and more.', 'icon' => 'archive', 'href' => '#'],
      ['title' => 'Documentation', 'desc' => 'Comprehensive docs with search, examples, and guided navigation.', 'icon' => 'book-open', 'href' => '/docs/introduction'],
      ['title' => 'Community', 'desc' => 'GitHub discussions, issues, and contributions from the community.', 'icon' => 'users', 'href' => 'https://github.com/RITH-1437/ZeroPing/discussions'],
    ];
    foreach ($ecosystem as $item): ?>
    <a href="<?= $item['href'] ?>" class="group relative rounded-2xl border border-zp-border bg-zp-surface/30 p-5 hover:bg-zp-surface hover:border-cyan-500/20 transition-all duration-300 <?= !empty($item['disabled']) ? 'opacity-40 pointer-events-none' : '' ?>">
      <div class="flex items-center gap-3">
        <div class="flex items-center justify-center h-9 w-9 rounded-lg bg-zp-surface border border-zp-border text-zp-muted group-hover:text-zp-link group-hover:border-cyan-500/30 transition-all">
          <?php
          $ecoIcons = [
            'package' => '<svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 10.5h.375c.621 0 1.125.504 1.125 1.125v2.25c0 .621-.504 1.125-1.125 1.125H21M4.5 10.5H18V7.5a2.25 2.25 0 00-2.25-2.25H6.75A2.25 2.25 0 004.5 7.5v3zm0 0v6.75A2.25 2.25 0 006.75 19.5h10.5a2.25 2.25 0 002.25-2.25v-1.5M4.5 10.5H3a1.5 1.5 0 000 3h1.5"/></svg>',
            'bar-chart' => '<svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/></svg>',
            'terminal' => '<svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 7.5l3 2.25-3 2.25m4.5 0h3m-9 8.25h13.5A2.25 2.25 0 0021 18V6a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 6v12a2.25 2.25 0 002.25 2.25z"/></svg>',
            'archive' => '<svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0l-3-3m3 3l3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/></svg>',
            'book-open' => '<svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>',
            'users' => '<svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>',
          ];
          echo $ecoIcons[$item['icon']];
          ?>
        </div>
        <div>
          <h3 class="text-sm font-semibold text-zp-ink"><?= $item['title'] ?></h3>
          <p class="text-xs text-zp-desc mt-0.5"><?= $item['desc'] ?></p>
        </div>
      </div>
    </a>
    <?php endforeach; ?>
  </div>
</section>

<!-- Benchmarks -->
<section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-24" data-animate>
  <div class="rounded-2xl border border-zp-border bg-zp-surface/50 p-8 sm:p-12">
    <div class="text-center">
      <h2 class="font-display text-4xl sm:text-5xl font-bold text-zp-ink tracking-tight">Performance</h2>
      <p class="mt-4 text-lg text-zp-desc max-w-2xl mx-auto">ZeroPing is built for speed — from routing to response, every microsecond counts.</p>
    </div>
    <div class="mt-12 grid sm:grid-cols-3 gap-8">
      <div class="text-center">
        <div class="text-5xl font-display font-bold text-zp-link">0.8ms</div>
        <p class="mt-2 text-sm text-zp-muted">Average routing time</p>
        <div class="mt-4 h-2 rounded-full bg-zp-border overflow-hidden">
          <div class="h-full w-4/5 rounded-full bg-zp-primary animate-pulse-glow"></div>
        </div>
      </div>
      <div class="text-center">
        <div class="text-5xl font-display font-bold text-emerald-400">12MB</div>
        <p class="mt-2 text-sm text-zp-muted">Memory per request</p>
        <div class="mt-4 h-2 rounded-full bg-zp-border overflow-hidden">
          <div class="h-full w-3/5 rounded-full bg-zp-primary animate-pulse-glow" style="animation-delay: 1s;"></div>
        </div>
      </div>
      <div class="text-center">
        <div class="text-5xl font-display font-bold text-zp-link">8ms</div>
        <p class="mt-2 text-sm text-zp-muted">Startup to response</p>
        <div class="mt-4 h-2 rounded-full bg-zp-border overflow-hidden">
          <div class="h-full w-9/10 rounded-full bg-zp-primary animate-pulse-glow" style="animation-delay: 2s;"></div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-24" data-animate>
  <div class="relative rounded-3xl border border-zp-border bg-zp-surface overflow-hidden p-12 sm:p-16 text-center">
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[40rem] h-[20rem] bg-zp-link/5 blur-3xl"></div>
    <div class="relative">
      <h2 class="font-display text-4xl sm:text-5xl font-bold text-zp-ink tracking-tight">Ready to build something great?</h2>
      <p class="mt-4 text-lg text-zp-desc max-w-xl mx-auto">Start your next PHP project with ZeroPing and experience modern development.</p>
      <div class="mt-10 flex flex-wrap justify-center gap-4">
        <a href="/installation" class="inline-flex items-center gap-2 rounded-2xl bg-zp-primary px-6 py-3.5 text-sm font-semibold text-zp-ink shadow-md shadow-zp-primary/20 hover:bg-zp-primary-hover transition-all duration-200 focus-ring">
          Get Started Now
          <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
        </a>
        <a href="https://github.com/RITH-1437/ZeroPing" target="_blank" rel="noopener" class="inline-flex items-center gap-2 rounded-2xl border border-zp-border bg-zp-surface/50 px-6 py-3.5 text-sm font-semibold text-zp-ink hover:bg-zp-surface hover:border-cyan-500/30 transition-all duration-200 focus-ring">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M12 .5C5.7.5.5 5.7.5 12c0 5.1 3.3 9.4 7.9 10.9.6.1.8-.2.8-.5v-2c-3.2.7-3.9-1.4-3.9-1.4-.5-1.3-1.3-1.7-1.3-1.7-1-.7.1-.7.1-.7 1.2.1 1.8 1.2 1.8 1.2 1 1.8 2.7 1.3 3.4 1 .1-.8.4-1.3.7-1.6-2.6-.3-5.3-1.3-5.3-5.8 0-1.3.5-2.3 1.2-3.1-.1-.3-.5-1.5.1-3.1 0 0 1-.3 3.3 1.2a11.5 11.5 0 0 1 6 0C17.3 4.7 18.3 5 18.3 5c.6 1.6.2 2.8.1 3.1.8.8 1.2 1.8 1.2 3.1 0 4.5-2.7 5.5-5.3 5.8.4.4.8 1.1.8 2.2v3.3c0 .3.2.6.8.5A11.5 11.5 0 0 0 23.5 12C23.5 5.7 18.3.5 12 .5z"/></svg>
          Star on GitHub
        </a>
      </div>
    </div>
  </div>
</section>
