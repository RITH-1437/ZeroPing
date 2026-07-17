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
      ['icon' => 'router.png', 'title' => 'Fast Routing', 'desc' => 'Expressive, regex-powered router with named routes, groups, prefixes, and middleware stacks.'],
      ['icon' => 'database.png', 'title' => 'Modern ORM', 'desc' => 'Active Record ORM with relationships, accessors, mutators, pagination, and soft deletes.'],
      ['icon' => 'cli.png', 'title' => 'Powerful CLI', 'desc' => 'Zero CLI — scaffold, migrate, test, and manage your app from a single command surface.'],
      ['icon' => 'authentication.png', 'title' => 'Secure by Default', 'desc' => 'CSRF protection, encryption, rate limiting, signed URLs, and HTML escaping out of the box.'],
      ['icon' => 'testing.png', 'title' => 'Built-in Testing', 'desc' => 'HTTP assertions, database transactions, test factories, and a dedicated test runner.'],
      ['icon' => 'documentation.png', 'title' => 'Beautiful Docs', 'desc' => 'Searchable documentation with code examples, dark mode, and interactive navigation.'],
    ];
    foreach ($features as $f): ?>
    <article class="group relative rounded-2xl border border-zp-border bg-zp-surface/50 p-6 hover:bg-zp-surface hover:border-cyan-500/20 hover:shadow-lg hover:shadow-cyan-500/5 transition-all duration-300">
      <div class="absolute inset-0 rounded-2xl bg-zp-primary/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
      <div class="relative">
        <div class="inline-flex items-center justify-center h-11 w-11 rounded-xl bg-zp-surface border border-zp-border text-zp-link transition-all duration-300 group-hover:scale-110 group-hover:border-cyan-500/40 group-hover:shadow-lg group-hover:shadow-cyan-500/10">
          <img src="/assets/images/<?= $f['icon'] ?>" alt="<?= $f['title'] ?>" class="h-6 w-6 object-contain" loading="lazy">
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
      ['title' => 'ZeroPing Framework', 'desc' => 'Core framework with MVC, ORM, CLI, and all batteries included.', 'icon' => 'package.png', 'href' => '/'],
      ['title' => 'ZeroPing Arena', 'desc' => 'Performance benchmarking and optimization playground.', 'icon' => 'core.png', 'href' => '/arena', 'disabled' => true],
      ['title' => 'CLI Tooling', 'desc' => 'Scaffolding, migrations, testing, and maintenance from the terminal.', 'icon' => 'cli.png', 'href' => '#'],
      ['title' => 'Packages', 'desc' => 'Modular packages extending ZeroPing with queues, mail, and more.', 'icon' => 'container.png', 'href' => '#'],
      ['title' => 'Documentation', 'desc' => 'Comprehensive docs with search, examples, and guided navigation.', 'icon' => 'documentation.png', 'href' => '/docs/introduction'],
      ['title' => 'Community', 'desc' => 'GitHub discussions, issues, and contributions from the community.', 'icon' => 'community.png', 'href' => 'https://github.com/RITH-1437/ZeroPing/discussions'],
    ];
    foreach ($ecosystem as $item): ?>
    <a href="<?= !empty($item['disabled']) ? '#' : $item['href'] ?>" class="group relative rounded-2xl border border-zp-border bg-zp-surface/30 p-5 hover:bg-zp-surface hover:border-cyan-500/20 transition-all duration-300 <?= !empty($item['disabled']) ? 'opacity-40 pointer-events-none' : '' ?>">
      <div class="flex items-center gap-3">
        <div class="flex items-center justify-center h-9 w-9 rounded-lg bg-zp-surface border border-zp-border text-zp-muted group-hover:text-zp-link group-hover:border-cyan-500/30 transition-all">
          <img src="/assets/images/<?= $item['icon'] ?>" alt="<?= $item['title'] ?>" class="h-5 w-5 object-contain" loading="lazy">
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
          <img src="/assets/images/github.png" alt="GitHub" class="h-[18px] w-[18px] object-contain">
          Star on GitHub
        </a>
      </div>
    </div>
  </div>
</section>
