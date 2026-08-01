<?php

namespace FrameworkSite;

use App\Core\View\Controller;
use App\Core\View\View;

/** Official ZeroPing framework website controller. */
class WebsiteController extends Controller
{
    public function __construct()
    {
        View::setBasePath(dirname(__DIR__));
    }

    public function home(): string
    {
        return $this->view('site/home', [
            'title' => 'ZeroPing — The calm, capable PHP framework',
            'active' => 'home',
            'stats' => [
                ['label' => 'CLI Commands', 'value' => '30+', 'icon' => '/assets/images/cli.png'],
                ['label' => 'Core Modules', 'value' => '18', 'icon' => '/assets/images/core.png'],
                ['label' => 'Average Setup Time', 'value' => '< 3 min', 'icon' => '/assets/images/time.png'],
                ['label' => 'Framework Version', 'value' => 'v' . \App\Core\Application\App::VERSION, 'icon' => '/assets/images/version.png'],
            ],
        ], 'site');
    }

    public function arena(): string
    {
        $catalog = new ArenaCatalog();

        return $this->view('site/arena', $catalog->all() + [
            'title' => 'ZeroPing Arena — Build Faster. Ship Smarter.',
            'description' => 'Explore the ZeroPing developer ecosystem: packages, playgrounds, benchmarks, CLI workflows, community, and product roadmap.',
            'active' => 'arena',
            'bodyClass' => 'arena-body',
            'pageStyles' => ['/assets/css/arena.css'],
            'pageScripts' => ['/assets/js/arena.js'],
            'footerComponent' => 'arena-terminal-footer',
        ], 'site');
    }

    public function features(): string { return $this->view('site/features', ['title' => 'Features — ZeroPing', 'active' => 'features'], 'site'); }

    public function documentation(): string
    {
        $docs = new DocsService();
        return $this->view('site/documentation-index', ['title' => 'Documentation — ZeroPing', 'active' => 'documentation', 'documents' => $docs->documents()], 'site');
    }

    public function installation(): string { return $this->view('site/installation', ['title' => 'Install ZeroPing', 'active' => 'installation'], 'site'); }
    public function gettingStarted(): string { return $this->view('site/getting-started', ['title' => 'Getting Started — ZeroPing', 'active' => 'getting-started'], 'site'); }
    public function api(): string { return $this->view('site/api', ['title' => 'API Reference — ZeroPing', 'active' => 'api'], 'site'); }
    public function roadmap(): string { return $this->view('site/roadmap', ['title' => 'Roadmap — ZeroPing', 'active' => 'roadmap'], 'site'); }

    public function github(): string
    {
        return $this->view('site/github', ['title' => 'GitHub — ZeroPing', 'active' => 'github', 'repositoryUrl' => 'https://github.com/RITH-1437/ZeroPing'], 'site');
    }

    public function community(): string
    {
        return $this->view('site/community', [
            'title' => 'Community — ZeroPing', 'active' => 'community',
            'repositoryUrl' => 'https://github.com/RITH-1437/ZeroPing',
            'discussionsUrl' => 'https://github.com/RITH-1437/ZeroPing/discussions',
        ], 'site');
    }

    public function packages(): string
    {
        return $this->hub('packages', 'Packages', 'Composable tools, one cohesive workflow.', 'Install first-party extensions with confidence. Each package follows ZeroPing conventions, ships with docs, and keeps your application dependency graph intentional.', [
            ['label' => 'Browse package docs', 'href' => '/docs/introduction'], ['label' => 'View source', 'href' => 'https://github.com/RITH-1437/ZeroPing'],
        ], [
            ['title' => 'First-party packages', 'intro' => 'Stable building blocks maintained alongside the framework.', 'items' => [
                ['title' => 'Queue', 'tag' => 'Available', 'description' => 'Dispatch, delay, retry, and work through background jobs with sync and database drivers.', 'href' => '/docs/queues', 'cta' => 'Read queue docs'],
                ['title' => 'Support', 'tag' => 'Available', 'description' => 'Package service providers, configuration merging, migrations, views, and command registration.', 'href' => '/docs/extending', 'cta' => 'Learn to extend'],
                ['title' => 'Starter templates', 'tag' => 'Available', 'description' => 'Begin with a focused empty app, MVC baseline, API, blog, or full starter template.', 'href' => '/docs/starter-templates', 'cta' => 'Explore templates'],
            ]],
            ['title' => 'Package workflow', 'intro' => 'Keep adoption visible and repeatable from the terminal.', 'items' => [
                ['title' => 'Discover and install', 'tag' => 'CLI', 'description' => 'Use the package command surface to list, enable, and manage installed extensions.', 'code' => "php zero package:list\nphp zero package:install vendor/package\nphp zero optimize"],
            ]],
        ]);
    }

    public function examples(): string
    {
        return $this->hub('examples', 'Examples', 'Patterns you can understand in one sitting.', 'Copy a focused starting point, then follow the implementation through routes, controllers, models, views, and tests. Every example is designed for the first productive hour.', [
            ['label' => 'Create a project', 'href' => '/installation'], ['label' => 'Read the API', 'href' => '/api'],
        ], [
            ['title' => 'Start from a familiar shape', 'intro' => 'Choose a pattern based on the application you are building.', 'items' => [
                ['title' => 'MVC application', 'tag' => 'Beginner', 'description' => 'A route, a controller action, and a view—the smallest complete vertical slice.', 'href' => '/getting-started', 'cta' => 'Build this flow'],
                ['title' => 'JSON API', 'tag' => 'API', 'description' => 'Structure endpoints around resources, validation, and consistent responses.', 'href' => '/api', 'cta' => 'Open API patterns'],
                ['title' => 'Blog starter', 'tag' => 'Template', 'description' => 'Start with posts, migrations, models, and a clean public reading surface.', 'href' => '/docs/starter-templates', 'cta' => 'See starter templates'],
            ]],
            ['title' => 'A complete first endpoint', 'intro' => 'The same conventions carry from a prototype to a larger application.', 'items' => [
                ['title' => 'Route to response', 'tag' => 'PHP', 'description' => 'Register a named route and keep the action close to the behavior it serves.', 'code' => "use App\\Core\\Routing\\Router;\nuse App\\Controllers\\ProjectController;\n\nRouter::get('/projects/{id}', [ProjectController::class, 'show'])\n    ->name('projects.show');"],
            ]],
        ]);
    }

    public function changelog(): string
    {
        return $this->hub('changelog', 'Changelog', 'A clear record of what changed and why.', 'Follow release notes, migration-impacting changes, and the work that improves the everyday developer loop.', [
            ['label' => 'View roadmap', 'href' => '/roadmap'], ['label' => 'Follow on GitHub', 'href' => 'https://github.com/RITH-1437/ZeroPing'],
        ], [
            ['title' => 'Latest releases', 'intro' => 'Changes are grouped by outcome rather than a raw commit stream.', 'items' => [
                ['title' => 'v2.0.1', 'tag' => 'Current', 'description' => 'Expanded documentation coverage, package standardization, version tracking, and a refined developer-facing website.', 'href' => '/roadmap', 'cta' => 'See roadmap context'],
                ['title' => 'v2.0.0', 'tag' => 'Released', 'description' => 'Localization, WebSocket foundations, sliding-window rate limiting, health checks, and an extensible plugin architecture.', 'href' => '/roadmap', 'cta' => 'Explore v2.0'],
                ['title' => 'v1.5.0', 'tag' => 'Released', 'description' => 'Relationships, queues, mail, testing tools, profiling, API resources, and stronger security utilities.', 'href' => '/roadmap', 'cta' => 'Review milestone'],
            ]],
            ['title' => 'Upgrade intentionally', 'intro' => 'Before updating, verify the project and run the test suite in your own environment.', 'items' => [
                ['title' => 'Release checklist', 'tag' => 'CLI', 'description' => 'A short routine to keep framework upgrades deliberate and reversible.', 'code' => "composer update rith-1437/zeroping\nphp zero doctor\nphp zero test\nphp zero optimize"],
            ]],
        ]);
    }

    public function blog(): string
    {
        return $this->hub('blog', 'The ZeroPing Journal', 'Engineering notes are on their way.', 'The blog will cover framework decisions, practical PHP patterns, package releases, and stories from the community. Until launch, the changelog and roadmap are the best places to follow progress.', [
            ['label' => 'Read the changelog', 'href' => '/changelog'], ['label' => 'Explore the roadmap', 'href' => '/roadmap'],
        ], [
            ['title' => 'What to expect', 'intro' => 'A useful publication, not a content feed.', 'items' => [
                ['title' => 'Release stories', 'tag' => 'Soon', 'description' => 'The reasoning behind changes, trade-offs, and upgrade guidance for each meaningful release.'],
                ['title' => 'Build notes', 'tag' => 'Soon', 'description' => 'Small, concrete explorations of routing, database work, testing, performance, and package design.'],
                ['title' => 'Community spotlight', 'tag' => 'Soon', 'description' => 'Practical projects and contributions from people building with ZeroPing.'],
            ]],
        ]);
    }

    public function sponsors(): string
    {
        return $this->hub('sponsors', 'Sponsors', 'Help keep the framework focused and sustainable.', 'ZeroPing is free and open source. Future sponsorship will support release work, documentation, infrastructure, and reliable maintenance without compromising the project’s independence.', [
            ['label' => 'Join the community', 'href' => '/community'], ['label' => 'Star on GitHub', 'href' => 'https://github.com/RITH-1437/ZeroPing'],
        ], [
            ['title' => 'A transparent path to sponsorship', 'intro' => 'Sponsorship tooling is being prepared. The commitment is to make support clear and useful.', 'items' => [
                ['title' => 'Individual supporters', 'tag' => 'Planned', 'description' => 'A simple way to back regular maintenance and receive acknowledgement on the project site.'],
                ['title' => 'Team sponsors', 'tag' => 'Planned', 'description' => 'A route for teams relying on ZeroPing to fund stability, docs, and long-term stewardship.'],
                ['title' => 'Contribution-first support', 'tag' => 'Now', 'description' => 'The best support today: improve docs, share feedback, report issues, and help other developers succeed.', 'href' => '/community', 'cta' => 'Contribute today'],
            ]],
        ]);
    }

    public function docs(string $slug = 'introduction'): string
    {
        $docs = new DocsService();
        $doc = $docs->find($slug);
        if (!$doc) {
            http_response_code(404);
            return $this->view('errors/404', ['title' => 'Not Found', 'active' => 'documentation'], 'site');
        }
        $rendered = $docs->render($docs->loadMarkdown($slug) ?? '# Missing document');
        $neighbors = $docs->neighbors($slug);
        return $this->view('site/documentation', [
            'title' => $doc['title'] . ' — ZeroPing Docs', 'active' => 'documentation', 'documents' => $docs->documents(), 'currentDoc' => $doc,
            'docHtml' => $rendered['html'], 'toc' => $rendered['toc'], 'previous' => $neighbors['previous'], 'next' => $neighbors['next'],
        ], 'site');
    }

    public function showcase(): string
    {
        return $this->hub('showcase', 'Showcase', 'Built with ZeroPing.', 'Real applications, projects, and demos built by developers around the world using ZeroPing. From personal projects to production APIs — see what the community is building.', [
            ['label' => 'Share your project', 'href' => 'https://github.com/RITH-1437/ZeroPing/discussions'],
            ['label' => 'View community', 'href' => '/community'],
        ], [
            ['title' => 'Community projects', 'intro' => 'Submit your project to appear here. Open a GitHub Discussion tagged "showcase".', 'items' => [
                ['title' => 'ZeroPing Framework Site', 'tag' => 'Official', 'description' => 'This very site — built on ZeroPing itself. Documentation, arena, package browser, and community pages all served by the framework.', 'href' => 'https://github.com/RITH-1437/ZeroPing', 'cta' => 'View source'],
                ['title' => 'Submit your project', 'tag' => 'Open', 'description' => 'Share what you have built. Accepted projects will be listed here with a link to the source or live site.', 'href' => 'https://github.com/RITH-1437/ZeroPing/discussions', 'cta' => 'Submit now'],
            ]],
            ['title' => 'Templates you can deploy today', 'intro' => 'Five starter templates are ready to scaffold a new project in under three minutes.', 'items' => [
                ['title' => 'MVC Starter', 'tag' => 'Template', 'description' => 'Full CRUD application with user management, views, migrations, and a working layout.', 'href' => '/docs/starter-templates', 'cta' => 'Use template'],
                ['title' => 'REST API', 'tag' => 'Template', 'description' => 'JSON API project with authentication boilerplate, resource classes, and route grouping.', 'href' => '/docs/starter-templates', 'cta' => 'Use template'],
                ['title' => 'Blog', 'tag' => 'Template', 'description' => 'Blog with posts, categories, pagination, and a clean public reading surface.', 'href' => '/docs/starter-templates', 'cta' => 'Use template'],
            ]],
        ]);
    }

    public function deploy(): string
    {
        return $this->hub('deploy', 'ZeroPing Deploy', 'One command. Live in seconds.', 'Deployment automation for ZeroPing applications is in active development. Deploy connects your repository to your servers, runs your migrations, and keeps your workers healthy automatically.', [
            ['label' => 'Follow progress', 'href' => '/roadmap'],
            ['label' => 'Read deployment docs', 'href' => '/docs/introduction'],
        ], [
            ['title' => 'Planned capabilities', 'intro' => 'The deployment surface is designed to feel as natural as the CLI.', 'items' => [
                ['title' => 'Zero-downtime deploys', 'tag' => 'Planned', 'description' => 'Atomic release directories with symlink cutover — no dropped requests during deployment.'],
                ['title' => 'Migration automation', 'tag' => 'Planned', 'description' => 'Migrations run as part of the deploy pipeline, with rollback on failure.'],
                ['title' => 'Worker lifecycle', 'tag' => 'Planned', 'description' => 'Queue workers and schedulers restart gracefully after each deployment.'],
                ['title' => 'Health checks', 'tag' => 'Planned', 'description' => 'Automated pre and post-deploy health checks via the `php zero doctor` surface.'],
            ]],
            ['title' => 'Deploy today with the CLI', 'intro' => 'While ZeroPing Deploy is in development, you can deploy reliably using existing tooling.', 'items' => [
                ['title' => 'Production checklist', 'tag' => 'CLI', 'description' => 'Run this sequence on every deployment to ensure a consistent, optimized production state.', 'code' => "git pull origin main\ncomposer install --no-dev --optimize-autoloader\nphp zero migrate\nphp zero optimize\nphp zero doctor"],
            ]],
        ]);
    }

    public function studio(): string
    {
        return $this->hub('studio', 'ZeroPing Studio', 'Visual tools for ZeroPing applications.', 'Studio brings a visual admin layer to your ZeroPing app: browse models, monitor queues, inspect scheduled jobs, tail logs, and manage configuration — all from a browser-based interface.', [
            ['label' => 'Follow progress', 'href' => '/roadmap'],
            ['label' => 'Star on GitHub', 'href' => 'https://github.com/RITH-1437/ZeroPing'],
        ], [
            ['title' => 'Planned panels', 'intro' => 'Studio is a layered visual interface that maps directly to the framework internals.', 'items' => [
                ['title' => 'Model browser', 'tag' => 'Planned', 'description' => 'Browse, filter, and inspect database records with full relationship expansion.'],
                ['title' => 'Queue monitor', 'tag' => 'Planned', 'description' => 'Track pending, running, and failed jobs. Retry or discard from the UI.'],
                ['title' => 'Schedule viewer', 'tag' => 'Planned', 'description' => 'See all registered scheduled events, their cron expressions, and last-run times.'],
                ['title' => 'Log explorer', 'tag' => 'Planned', 'description' => 'Tail, search, and filter log channels without leaving the browser.'],
            ]],
            ['title' => 'Available today', 'intro' => 'The Debug Toolbar and CLI surface many of the same insights right now.', 'items' => [
                ['title' => 'Debug Toolbar', 'tag' => 'Available', 'description' => 'Per-request SQL queries, memory usage, routing details, and performance timings in development.', 'href' => '/docs/introduction', 'cta' => 'Enable toolbar'],
                ['title' => 'Monitor command', 'tag' => 'Available', 'description' => 'Real-time application health, service status, and queue depth from the terminal.', 'code' => 'php zero monitor'],
            ]],
        ]);
    }

    public function cloud(): string
    {
        return $this->hub('cloud', 'ZeroPing Cloud', 'Managed hosting for PHP applications.', 'ZeroPing Cloud will be managed hosting designed specifically for ZeroPing applications: automatic deployments from Git, isolated environments, global CDN, queue workers, and scheduler management — with no server knowledge required.', [
            ['label' => 'Follow progress', 'href' => '/roadmap'],
            ['label' => 'Join the community', 'href' => '/community'],
        ], [
            ['title' => 'What Cloud will offer', 'intro' => 'A deployment target built to match the framework\'s developer experience.', 'items' => [
                ['title' => 'Git-based deployments', 'tag' => 'Planned', 'description' => 'Push to deploy. Every branch gets its own isolated preview environment automatically.'],
                ['title' => 'Managed databases', 'tag' => 'Planned', 'description' => 'MySQL and PostgreSQL databases provisioned and backed up automatically.'],
                ['title' => 'Queue workers & scheduler', 'tag' => 'Planned', 'description' => 'Long-running workers and cron tasks managed by the platform, not your ops team.'],
                ['title' => 'Edge caching', 'tag' => 'Planned', 'description' => 'Static asset distribution and response caching at the edge for every deployment.'],
            ]],
            ['title' => 'In the meantime', 'intro' => 'ZeroPing runs cleanly on any PHP hosting environment.', 'items' => [
                ['title' => 'Docker-ready', 'tag' => 'Available', 'description' => 'The repository ships a production-ready Dockerfile and Compose config for container deployments.', 'href' => '/docs/introduction', 'cta' => 'Read deployment guide'],
            ]],
        ]);
    }

    public function forge(): string
    {
        return $this->hub('forge', 'ZeroPing Forge', 'Server provisioning and site management.', 'Forge automates server setup for PHP applications: provision a new Ubuntu server, install PHP, MySQL, nginx, and Supervisor, then manage sites, SSL certificates, and deployments from a clean web interface.', [
            ['label' => 'Follow progress', 'href' => '/roadmap'],
            ['label' => 'Star on GitHub', 'href' => 'https://github.com/RITH-1437/ZeroPing'],
        ], [
            ['title' => 'Planned features', 'intro' => 'Forge targets developers who prefer self-managed servers with an automated setup layer.', 'items' => [
                ['title' => 'One-click server provisioning', 'tag' => 'Planned', 'description' => 'Connect your cloud account and provision a fully configured PHP server in minutes.'],
                ['title' => 'SSL & domain management', 'tag' => 'Planned', 'description' => 'Automated Let\'s Encrypt SSL certificates and DNS management.'],
                ['title' => 'Site & process management', 'tag' => 'Planned', 'description' => 'Manage nginx sites, queue workers, and scheduler processes through a web UI.'],
                ['title' => 'Deployment hooks', 'tag' => 'Planned', 'description' => 'Trigger deployments from GitHub webhooks, running migrations and cache rebuilds automatically.'],
            ]],
            ['title' => 'Start without Forge', 'intro' => 'Everything Forge will automate can be done manually with the CLI today.', 'items' => [
                ['title' => 'Docker deployment', 'tag' => 'Available', 'description' => 'Production-ready Docker and Compose configuration are included in every ZeroPing project.', 'href' => '/docs/introduction', 'cta' => 'Docker guide'],
            ]],
        ]);
    }

    /** @param array<int, array<string, mixed>> $actions @param array<int, array<string, mixed>> $sections */
    private function hub(string $active, string $eyebrow, string $headline, string $description, array $actions, array $sections): string
    {
        return $this->view('site/hub', compact('active', 'eyebrow', 'headline', 'description', 'actions', 'sections') + ['title' => $eyebrow . ' — ZeroPing'], 'site');
    }
}
