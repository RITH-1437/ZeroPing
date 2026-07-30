<?php

declare(strict_types=1);

namespace FrameworkSite;

use App\Core\Application\App;

/**
 * Curated product data for the Arena portal.
 *
 * Keeping the catalog outside the template makes it straightforward to replace
 * individual sections with registry, benchmark, or GitHub API data later.
 */
final class ArenaCatalog
{
    /** @return array<string, mixed> */
    public function all(): array
    {
        return [
            'stats' => $this->stats(),
            'ecosystem' => $this->ecosystem(),
            'playground' => $this->playground(),
            'packages' => $this->packages(),
            'roadmap' => $this->roadmap(),
            'commands' => $this->commands(),
        ];
    }

    /** @return array<int, array<string, string|int>> */
    private function stats(): array
    {
        return [
            ['label' => 'Framework version', 'value' => App::VERSION, 'number' => 201, 'format' => 'version'],
            ['label' => 'Packagist downloads', 'value' => '20', 'number' => 20, 'format' => 'number'],
            ['label' => 'GitHub stars', 'value' => '1', 'number' => 1, 'format' => 'number'],
            ['label' => 'Contributors', 'value' => '1', 'number' => 1, 'format' => 'number'],
            ['label' => 'Official packages', 'value' => '15', 'number' => 15, 'format' => 'number'],
            ['label' => 'Documentation pages', 'value' => '20+', 'number' => 20, 'format' => 'plus'],
        ];
    }

    /** @return array<int, array<string, string>> */
    private function ecosystem(): array
    {
        return [
            ['icon' => 'framework', 'title' => 'Framework', 'description' => 'A focused MVC foundation with expressive routing, ORM, validation, and security.', 'status' => 'Stable', 'tone' => 'stable', 'href' => '/features'],
            ['icon' => 'terminal', 'title' => 'CLI', 'description' => 'Generate, migrate, test, inspect, and optimize from one command surface.', 'status' => 'Stable', 'tone' => 'stable', 'href' => '/docs/cli'],
            ['icon' => 'arena', 'title' => 'Arena', 'description' => 'Explore benchmarks, packages, examples, and the complete developer platform.', 'status' => 'Preview', 'tone' => 'preview', 'href' => '#playground'],
            ['icon' => 'package', 'title' => 'Packages', 'description' => 'First-party modules that follow the same conventions as the framework.', 'status' => 'Growing', 'tone' => 'active', 'href' => '#packages'],
            ['icon' => 'book', 'title' => 'Documentation', 'description' => 'Searchable guides from installation through production deployment.', 'status' => 'Available', 'tone' => 'stable', 'href' => '/docs/introduction'],
            ['icon' => 'code', 'title' => 'Examples', 'description' => 'Complete patterns for MVC apps, APIs, queues, validation, and testing.', 'status' => 'Available', 'tone' => 'stable', 'href' => '/examples'],
            ['icon' => 'spark', 'title' => 'Starter Kits', 'description' => 'Start from empty, MVC, API, blog, or a full application baseline.', 'status' => 'Available', 'tone' => 'stable', 'href' => '/docs/starter-templates'],
            ['icon' => 'layout', 'title' => 'Templates', 'description' => 'Curated application shapes with sensible defaults and no lock-in.', 'status' => 'Growing', 'tone' => 'active', 'href' => '/packages'],
            ['icon' => 'puzzle', 'title' => 'Extensions', 'description' => 'Add features through provider-driven, Composer-native extensions.', 'status' => 'Beta', 'tone' => 'preview', 'href' => '/docs/extending'],
            ['icon' => 'store', 'title' => 'Marketplace', 'description' => 'Discover trusted community packages and reusable application kits.', 'status' => 'Coming soon', 'tone' => 'soon', 'href' => '#roadmap'],
            ['icon' => 'graduation', 'title' => 'Learning', 'description' => 'Guided paths, tutorials, and practical framework architecture notes.', 'status' => 'In progress', 'tone' => 'preview', 'href' => '/getting-started'],
            ['icon' => 'users', 'title' => 'Community', 'description' => 'Build in the open through discussions, issues, and contributions.', 'status' => 'Open', 'tone' => 'stable', 'href' => '#community'],
            ['icon' => 'route', 'title' => 'Roadmap', 'description' => 'Follow the deliberate path from framework to complete platform.', 'status' => 'Public', 'tone' => 'stable', 'href' => '#roadmap'],
            ['icon' => 'history', 'title' => 'Release Notes', 'description' => 'Understand every release, migration impact, and design decision.', 'status' => 'Current', 'tone' => 'stable', 'href' => '/changelog'],
            ['icon' => 'gauge', 'title' => 'Benchmarks', 'description' => 'Inspect transparent performance scenarios instead of vanity numbers.', 'status' => 'Preview', 'tone' => 'preview', 'href' => '#benchmark-runner'],
            ['icon' => 'bolt', 'title' => 'Performance', 'description' => 'Profile routing, memory, ORM work, and framework boot behavior.', 'status' => 'Active', 'tone' => 'active', 'href' => '#performance'],
            ['icon' => 'tools', 'title' => 'Developer Tools', 'description' => 'Debug, scaffold, inspect, document, and release with confidence.', 'status' => 'Growing', 'tone' => 'active', 'href' => '#developer-experience'],
        ];
    }

    /** @return array<int, array<string, string>> */
    private function playground(): array
    {
        return [
            ['icon' => 'gauge', 'title' => 'Benchmark Runner', 'description' => 'Run a deterministic local scenario and compare framework operations.', 'status' => 'Interactive', 'tone' => 'active', 'preview' => 'benchmark'],
            ['icon' => 'chart', 'title' => 'Performance Comparison', 'description' => 'Compare versions and runtime profiles with reproducible methodology.', 'status' => 'Dataset planned', 'tone' => 'soon', 'preview' => 'bars'],
            ['icon' => 'memory', 'title' => 'Memory Usage', 'description' => 'Visualize bootstrap, route dispatch, query, and response allocations.', 'status' => 'Preview', 'tone' => 'preview', 'preview' => 'memory'],
            ['icon' => 'route', 'title' => 'Routing Speed', 'description' => 'Explore static, dynamic, grouped, and middleware route scenarios.', 'status' => 'Preview', 'tone' => 'preview', 'preview' => 'routing'],
            ['icon' => 'database', 'title' => 'ORM Benchmark', 'description' => 'Measure hydration, query building, relationships, and pagination.', 'status' => 'Planned', 'tone' => 'soon', 'preview' => 'orm'],
            ['icon' => 'database', 'title' => 'Database Playground', 'description' => 'Build safe sample queries against an isolated temporary database.', 'status' => 'Coming soon', 'tone' => 'soon', 'preview' => 'database'],
            ['icon' => 'globe', 'title' => 'HTTP Playground', 'description' => 'Compose requests and inspect headers, middleware, and responses.', 'status' => 'Prototype', 'tone' => 'preview', 'preview' => 'http'],
            ['icon' => 'package', 'title' => 'Package Sandbox', 'description' => 'Inspect discovery, configuration, providers, and publishable assets.', 'status' => 'Planned', 'tone' => 'soon', 'preview' => 'package'],
            ['icon' => 'layout', 'title' => 'Component Preview', 'description' => 'Review reusable UI patterns across themes and responsive states.', 'status' => 'Preview', 'tone' => 'preview', 'preview' => 'component'],
            ['icon' => 'code', 'title' => 'Interactive Examples', 'description' => 'Walk through routes, controllers, models, and tests as one flow.', 'status' => 'In progress', 'tone' => 'active', 'preview' => 'examples'],
            ['icon' => 'terminal', 'title' => 'Code Playground', 'description' => 'Experiment with framework APIs in a safe, guided environment.', 'status' => 'Research', 'tone' => 'soon', 'preview' => 'code'],
            ['icon' => 'spark', 'title' => 'AI Assistant', 'description' => 'A documentation-grounded assistant for learning and diagnostics.', 'status' => 'Future', 'tone' => 'soon', 'preview' => 'ai'],
        ];
    }

    /** @return array<int, array<string, string>> */
    private function packages(): array
    {
        $github = 'https://github.com/RITH-1437/ZeroPing';
        return [
            ['icon' => 'shield', 'name' => 'Authentication', 'slug' => 'auth', 'version' => 'Core', 'status' => 'Stable', 'tone' => 'stable', 'description' => 'Session guards, password hashing, and authentication flows.', 'docs' => '/docs/security', 'github' => $github],
            ['icon' => 'key', 'name' => 'Authorization', 'slug' => 'authorization', 'version' => 'Core', 'status' => 'Stable', 'tone' => 'stable', 'description' => 'Policies and application-level access decisions.', 'docs' => '/docs/security', 'github' => $github],
            ['icon' => 'mail', 'name' => 'Mail', 'slug' => 'mail', 'version' => 'v2.0', 'status' => 'Available', 'tone' => 'stable', 'description' => 'Driver-based messages, mailables, and queue integration.', 'docs' => '/docs/extending', 'github' => $github],
            ['icon' => 'queue', 'name' => 'Queue', 'slug' => 'queue', 'version' => 'v2.0', 'status' => 'Available', 'tone' => 'stable', 'description' => 'Background jobs with sync and database drivers.', 'docs' => '/docs/queues', 'github' => $github],
            ['icon' => 'bolt', 'name' => 'Events', 'slug' => 'events', 'version' => 'Core', 'status' => 'Stable', 'tone' => 'stable', 'description' => 'Decoupled events, listeners, and dispatching.', 'docs' => '/docs/extending', 'github' => $github],
            ['icon' => 'folder', 'name' => 'Storage', 'slug' => 'storage', 'version' => 'v2.0', 'status' => 'Available', 'tone' => 'stable', 'description' => 'Unified file operations and extensible disks.', 'docs' => '/docs/extending', 'github' => $github],
            ['icon' => 'check', 'name' => 'Validation', 'slug' => 'validation', 'version' => 'Core', 'status' => 'Stable', 'tone' => 'stable', 'description' => 'Fluent validation, form requests, and custom rules.', 'docs' => '/docs/validation', 'github' => $github],
            ['icon' => 'clock', 'name' => 'Scheduler', 'slug' => 'scheduler', 'version' => 'v2.0', 'status' => 'Available', 'tone' => 'stable', 'description' => 'Cron expressions, frequencies, and overlap protection.', 'docs' => '/docs/scheduler', 'github' => $github],
            ['icon' => 'bell', 'name' => 'Notifications', 'slug' => 'notifications', 'version' => 'v2.0', 'status' => 'Available', 'tone' => 'stable', 'description' => 'Mail and database notification channels.', 'docs' => '/docs/extending', 'github' => $github],
            ['icon' => 'search', 'name' => 'Search', 'slug' => 'search', 'version' => 'v2.5', 'status' => 'Planned', 'tone' => 'soon', 'description' => 'A portable full-text search abstraction.', 'docs' => '/roadmap', 'github' => $github],
            ['icon' => 'braces', 'name' => 'API Resources', 'slug' => 'api-resources', 'version' => 'v2.0', 'status' => 'Available', 'tone' => 'stable', 'description' => 'Consistent resource and collection transformation.', 'docs' => '/api', 'github' => $github],
            ['icon' => 'globe', 'name' => 'Localization', 'slug' => 'localization', 'version' => 'v2.0', 'status' => 'Available', 'tone' => 'stable', 'description' => 'Translations, fallback locales, and language files.', 'docs' => '/docs/extending', 'github' => $github],
            ['icon' => 'flask', 'name' => 'Testing', 'slug' => 'testing', 'version' => 'v2.0', 'status' => 'Available', 'tone' => 'stable', 'description' => 'HTTP assertions, test responses, and database tools.', 'docs' => '/docs/introduction', 'github' => $github],
            ['icon' => 'pages', 'name' => 'Pagination', 'slug' => 'pagination', 'version' => 'v2.5', 'status' => 'Planned', 'tone' => 'soon', 'description' => 'Offset and cursor pagination for ORM queries.', 'docs' => '/roadmap', 'github' => $github],
        ];
    }

    /** @return array<int, array<string, mixed>> */
    private function roadmap(): array
    {
        return [
            ['version' => 'v2.1', 'label' => 'Package foundation', 'status' => 'Now', 'tone' => 'current', 'items' => ['Official package extraction', 'Authentication starter kit', 'Arena registry foundation']],
            ['version' => 'v2.2', 'label' => 'Developer experience', 'status' => 'Next', 'tone' => 'next', 'items' => ['Testing utilities', 'API documentation generator', 'Advanced scaffolding']],
            ['version' => 'v2.5', 'label' => 'Ecosystem maturity', 'status' => 'Planned', 'tone' => 'planned', 'items' => ['Search and pagination', 'Community marketplace', 'Package quality signals']],
            ['version' => 'v3.0', 'label' => 'Platform services', 'status' => 'Vision', 'tone' => 'future', 'items' => ['Arena Cloud MVP', 'Async queue drivers', 'Monitoring and metrics']],
            ['version' => 'Future', 'label' => 'Scale without weight', 'status' => 'Research', 'tone' => 'future', 'items' => ['Serverless adapter', 'Multi-tenancy package', 'Documentation-grounded AI assistant']],
        ];
    }

    /** @return array<int, array<string, string>> */
    private function commands(): array
    {
        return [
            ['id' => 'composer', 'label' => 'Composer', 'title' => 'Install ZeroPing', 'command' => 'composer create-project rith-1437/zeroping my-app', 'detail' => 'Create a complete application through the standard PHP ecosystem.'],
            ['id' => 'create', 'label' => 'Create project', 'title' => 'Start with Zero CLI', 'command' => "php zero new my-app\ncd my-app\nphp zero serve", 'detail' => 'Scaffold, enter, and run your new app with one focused workflow.'],
            ['id' => 'controller', 'label' => 'Controller', 'title' => 'Generate application code', 'command' => 'php zero make:controller ProjectController', 'detail' => 'Generate code that follows the framework structure and conventions.'],
            ['id' => 'migrate', 'label' => 'Migrations', 'title' => 'Evolve the database', 'command' => "php zero make:migration create_projects_table\nphp zero migrate", 'detail' => 'Create and apply schema changes through repeatable migrations.'],
            ['id' => 'deploy', 'label' => 'Deploy', 'title' => 'Prepare for production', 'command' => "php zero test\nphp zero doctor\nphp zero optimize", 'detail' => 'Verify behavior, inspect the environment, and optimize production caches.'],
        ];
    }
}
