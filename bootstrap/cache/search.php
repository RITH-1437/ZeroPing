<?php

return array (
  0 =>
  array (
    'slug' => 'introduction',
    'title' => 'Introduction',
    'content' => 'IntroductionZeroPing is a modern PHP framework focused on clean architecture, predictable behavior, and a smooth developer experience.Core PhilosophyKeep framework APIs readable and explicit.Prefer composable modules over hidden magic.Ship practical tooling that supports real production workflows.Why ZeroPingZeroPing gives teams a lightweight but capable foundation:Routing, middleware, and controllersORM with models and relationshipsQueue, cache, mail, and scheduling subsystemsBuilt-in CLI commands for common development tasksTypical Project StructureCopyapp/
  Controllers/
  Models/
  Services/
config/
views/
public/Next StepContinue to the installation guide to bootstrap your first ZeroPing app.',
    'url' => '/docs/introduction',
  ),
  1 =>
  array (
    'slug' => 'installation',
    'title' => 'Installation',
    'content' => 'InstallationZeroPing is distributed as a Composer package. The recommended way to start a new project is composer create-project, which installs the framework, copies your environment file, generates an application key, prepares storage, and creates the default SQLite database — all with zero manual configuration.Requirementsctype, tokenizer, fileinfo, openssl, hash.file (or :memory:) is enough.',
    'url' => '/docs/installation',
  ),
  2 =>
  array (
    'slug' => 'installation',
    'title' => 'Installation',
    'content' => 'For production you can switch to MySQL, MariaDB, or PostgreSQL (see Database).PHP 8.1 or higher (tested through 8.5).The following PHP extensions: pdo, pdo_mysql, mbstring, json,Composer 2.x.A database. SQLite is the default and needs nothing installed — a single&gt; Windows, Linux, and macOS are all supported.',
    'url' => '/docs/installation',
  ),
  3 =>
  array (
    'slug' => 'installation',
    'title' => 'Installation',
    'content' => 'Paths are resolved with the &gt; platform directory separator, so the same commands work everywhere.Quick StartCopycomposer create-project rith-1437/zero-ping my-app
cd my-app
php zero serveThen open &lt;http://localhost:1437&gt;.',
    'url' => '/docs/installation',
  ),
  4 =>
  array (
    'slug' => 'installation',
    'title' => 'Installation',
    'content' => 'That is the entire setup for a working application — no server to provision, no credentials to type.&gt; Package name: rith-1437/zero-ping — all lowercase, hyphenated &gt; (the GitHub repository is RITH-1437/ZeroPing).What happens automaticallyAfter composer create-project, the post-create-project-cmd script runs and, without prompting, will:1. Verify the PHP version and required extensions. 2.',
    'url' => '/docs/installation',
  ),
  5 =>
  array (
    'slug' => 'installation',
    'title' => 'Installation',
    'content' => 'Create the runtime directories (storage/cache, storage/logs, bootstrap/cache, …) with writable permissions. 3. Copy .env.example to .env (you never copy it by hand). 4. Generate a random APP_KEY. 5. Create database/database.sqlite (the default database). 6. Optimise the Composer autoloader. 7.',
    'url' => '/docs/installation',
  ),
  6 =>
  array (
    'slug' => 'installation',
    'title' => 'Installation',
    'content' => 'Print a friendly summary with the next steps.If anything is missing (for example a required extension), the script reports a clean, actionable message instead of a stack trace.Interactive setup wizardPrefer to be guided?',
    'url' => '/docs/installation',
  ),
  7 =>
  array (
    'slug' => 'installation',
    'title' => 'Installation',
    'content' => 'Run the wizard after (or instead of) the automatic step:Copyphp zero installIt asks for your application name, environment, timezone, and database driver (SQLite recommended, MySQL, MariaDB, or PostgreSQL), validates the database connection before continuing, generates APP_KEY, and offers to run migrations.',
    'url' => '/docs/installation',
  ),
  8 =>
  array (
    'slug' => 'installation',
    'title' => 'Installation',
    'content' => 'Before finishing, the wizard validates your generated .env — confirming required keys are present, the timezone is valid (it falls back to UTC if not), and APP_URL/APP_DEBUG are well-formed — so you start from a known-good configuration.',
    'url' => '/docs/installation',
  ),
  9 =>
  array (
    'slug' => 'installation',
    'title' => 'Installation',
    'content' => 'A completion screen shows the next steps.Alternative: install from sourceIf you are contributing to the framework itself, clone the repository:Copygit clone https://github.com/rith-1437/zero-ping.git
cd ZeroPing
composer install
cp .env.example .env
php zero key:generateConfigurationConfiguration lives in config/*.php and is read from .env (environment variables).',
    'url' => '/docs/installation',
  ),
  10 =>
  array (
    'slug' => 'installation',
    'title' => 'Installation',
    'content' => 'Keep secrets in .env; the config files only reference them via env().The most important keys:| Key | Purpose | Default | |-----|---------|---------| | APP_NAME | Human-readable application name | ZeroPing | | APP_ENV | local, development, or production | production | | APP_DEBUG | Show detailed errors | false | | APP_KEY | Encryption key (auto-generated) | — | | APP_URL | Base URL | http://localhost:1437 | | APP_TIMEZONE | Default PHP timezone | UTC | | DB_CONNECTION | sqlite, mysql, mariadb, pgsql | sqlite |DatabaseZeroPing ships with four drivers.',
    'url' => '/docs/installation',
  ),
  11 =>
  array (
    'slug' => 'installation',
    'title' => 'Installation',
    'content' => 'New projects default to SQLite, so a fresh install works with no database server. Switching engines is a pure configuration change — no model, migration, or query code has to change.SQLite (default)CopyDB_CONNECTION=sqlite
DB_DATABASE=database/database.sqliteThat is the entire configuration. With no DB_DATABASE set, ZeroPing falls back to database/database.sqlite.',
    'url' => '/docs/installation',
  ),
  12 =>
  array (
    'slug' => 'installation',
    'title' => 'Installation',
    'content' => 'Use DB_DATABASE=:memory: for tests.MySQL / MariaDBCopyDB_CONNECTION=mysql          # or: mariadb
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=zero_ping
DB_USERNAME=root
DB_PASSWORD=secret
DB_CHARSET=utf8mb4
DB_COLLATION=utf8mb4_unicode_ciMariaDB uses the identical block with DB_CONNECTION=mariadb.PostgreSQLCopyDB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=zero_ping
DB_USERNAME=postgres
DB_PASSWORD=secret
DB_CHARSET=utf8
DB_SCHEMA=publicSee Database for the full reference, including how to run migrations against each engine.Starter TemplatesKick-start a project structure with a starter template:Copyphp zero new blog --name=&quot;My Blog&quot;Available templates: empty (minimal skeleton), mvc, blog, api, and dashboard.',
    'url' => '/docs/installation',
  ),
  13 =>
  array (
    'slug' => 'installation',
    'title' => 'Installation',
    'content' => 'Each scaffolds app/, config/, and views/ so you start from a sensible baseline. Use --dir= to scaffold into a specific folder.CLIZeroPing ships a batteries-included CLI (php zero).',
    'url' => '/docs/installation',
  ),
  14 =>
  array (
    'slug' => 'installation',
    'title' => 'Installation',
    'content' => 'The commands most relevant to installation and onboarding:| Command | Description | |---------|-------------| | php zero serve | Start the development server (default port 1437). | | php zero install | Interactive setup wizard. | | php zero doctor | Verify PHP, extensions, environment, and database. | | php zero migrate | Run database migrations. | | php zero key:generate | Generate APP_KEY. | | php zero new &lt;name&gt; | Scaffold a new app from a starter template. | | php zero make:controller | Scaffold a controller. | | php zero make:model | Scaffold a model. |Run php zero with no arguments to list every available command.Verify your installationRun the built-in doctor to confirm PHP, extensions, environment, and database connectivity are correctly configured:Copyphp zero doctorIt prints PASS, WARNING, and ERROR lines with colored output for each check: PHP version, Composer, PDO, the SQLite/MySQL/PostgreSQL extensions, required extensions, storage, cache, configuration, environment, and APP_KEY.Troubleshootingphp zero doctor reports &quot;PDO extension not loaded&quot;.',
    'url' => '/docs/installation',
  ),
  15 =>
  array (
    'slug' => 'installation',
    'title' => 'Installation',
    'content' => 'Enable pdo (and the driver you use: pdo_sqlite, pdo_mysql, or pdo_pgsql) in your php.ini, then re-run php zero doctor.&quot;Could not open the SQLite database&quot; / directory not writable. Make sure the project directory is writable, or create the database file manually: touch database/database.sqlite.The welcome page shows a 404. You are likely missing config/routes.php. Re-run composer create-project (or copy config/routes.php from a fresh install).',
    'url' => '/docs/installation',
  ),
  16 =>
  array (
    'slug' => 'installation',
    'title' => 'Installation',
    'content' => 'The default route points at the ZeroPing welcome controller.&quot;Controller App\\Controllers\\HomeController not found&quot;. You scaffolded with a starter template but have not regenerated the autoloader. Run composer dump-autoload.Migrations fail with a connection error. Check DB_CONNECTION and the related DB_* values in .env.',
    'url' => '/docs/installation',
  ),
  17 =>
  array (
    'slug' => 'installation',
    'title' => 'Installation',
    'content' => 'For MySQL or PostgreSQL, confirm the server is reachable and the credentials are correct. php zero doctor will point at the exact problem.FAQDo I need a database server to start? No. SQLite is the default and stores everything in a single file. You only need MySQL, MariaDB, or PostgreSQL when you choose them.Is the welcome page the real application? The welcome page is a built-in landing screen shown on a fresh install.',
    'url' => '/docs/installation',
  ),
  18 =>
  array (
    'slug' => 'installation',
    'title' => 'Installation',
    'content' => 'Edit config/routes.php and add controllers under app/Controllers to build your app.Can I change the database later? Yes — change DB_CONNECTION (and the related DB_* values) in .env. No code changes are required; the migrations table is created for the new engine automatically.Where do I put my environment secrets? In .env (already git-ignored). config/*.php reads them via env() and should never contain real credentials.ExamplesA minimal end-to-end flow:Copy# 1.',
    'url' => '/docs/installation',
  ),
  19 =>
  array (
    'slug' => 'installation',
    'title' => 'Installation',
    'content' => 'Create the project (SQLite, zero config)
composer create-project rith-1437/zero-ping blog
cd blog

# 2. Confirm the environment
php zero doctor

# 3. Run migrations
php zero migrate

# 4. Start developing
php zero serveSwitching to MySQL for production:Copy# .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=blog
DB_USERNAME=blog_user
DB_PASSWORD=strong-passwordCopyphp zero migrate:fresh   # recreate the schema on the new engine',
    'url' => '/docs/installation',
  ),
  20 =>
  array (
    'slug' => 'getting-started',
    'title' => 'Getting Started',
    'content' => 'Getting StartedBuild your first page by defining a route, controller action, and view.Define the routeCopyuse App\\Core\\Routing\\Router;
use App\\Controllers\\WebsiteController;

Router::get(&#039;/&#039;, [WebsiteController::class, &#039;home&#039;]);Create a controller actionCopypublic function home(): void
{
    $this-&gt;view(&#039;site/home&#039;, [&#039;title&#039; =&gt; &#039;ZeroPing&#039;]);
}Create a viewCopy&lt;h1&gt;Welcome to ZeroPing&lt;/h1&gt;
&lt;p&gt;You are ready to build.&lt;/p&gt;Add styling with TailwindUse Tailwind utility classes to ship responsive layouts quickly while keeping design consistent.',
    'url' => '/docs/getting-started',
  ),
  21 =>
  array (
    'slug' => 'api',
    'title' => 'API',
    'content' => 'APIThe ZeroPing API surface is intentionally concise and composable.RouterCopyRouter::get(&#039;/users&#039;, [UserController::class, &#039;index&#039;]);
Router::post(&#039;/users&#039;, [UserController::class, &#039;store&#039;]);ValidationCopy$validator = Validator::make($data, [
    &#039;email&#039; =&gt; &#039;required|email&#039;,
    &#039;password&#039; =&gt; &#039;required|min:6&#039;,
]);

if ($validator-&gt;fails()) {
    // Handle invalid request
}ORMCopy$user = User::create([
    &#039;first_name&#039; =&gt; &#039;Rin&#039;,
    &#039;last_name&#039; =&gt; &#039;Nairith&#039;,
    &#039;email&#039; =&gt; &#039;nairithrin143@gmail.com&#039;,
    &#039;password&#039; =&gt; PasswordHasher::make(&#039;secret123&#039;),
]);ResponsesCopyResponse::json([&#039;ok&#039; =&gt; true]);
Response::redirect(&#039;/dashboard&#039;);',
    'url' => '/docs/api',
  ),
  22 =>
  array (
    'slug' => 'cli',
    'title' => 'CLI Reference',
    'content' => 'CLI ReferenceZeroPing ships with a powerful CLI (php zero) for managing your application. Run php zero or php zero help to list every available command, colorized and grouped by purpose.',
    'url' => '/docs/cli',
  ),
  23 =>
  array (
    'slug' => 'cli',
    'title' => 'CLI Reference',
    'content' => 'Every command also supports --help (e.g. php zero make:model --help, or php zero help make:model) for usage, arguments, options, and examples.General Commands| Command | Description | |---|---| | php zero | Display the grouped, colorized command list | | php zero about | Display framework, PHP, Composer, environment, and driver info | | php zero version | Show the framework version | | php zero doctor | Verify the installation (PHP, extensions, env, key, database) | | php zero publish | Publish framework assets (config, views, lang, public) into your project | | php zero serve [port] | Start the development server (defaults to 1437) |Project Scaffolding| Command | Description | |---|---| | php zero new {type} | Create a new project from a template |Types: empty, blog, api, mvc, dashboardCopyphp zero new blog --name=&quot;My Blog&quot; --dir=./my-blogSearch| Command | Description | |---|---| | php zero search:index | Build the documentation search index |Migrations| Command | Description | |---|---| | php zero migrate | Run all pending migrations | | php zero migrate:fresh | Drop all tables and re-run migrations | | php zero migrate:refresh | Rollback all migrations then re-run them | | php zero migrate:rollback | Rollback the last migration | | php zero migrate:reset | Rollback all migrations | | php zero migrate:status | Show migration status |Make (Code Generation)| Command | Description | |---|---| | php zero make:model {name} | Create a new model | | php zero make:controller {name} | Create a new controller | | php zero make:service {name} | Create a new service class | | php zero make:repository {name} | Create a new repository | | php zero make:migration {name} | Create a new migration | | php zero make:mail {name} | Create a new mailable class | | php zero make:seeder {name} | Create a new database seeder | | php zero make:middleware {name} | Create a new HTTP middleware | | php zero make:request {name} | Create a new form request | | php zero make:policy {name} | Create a new authorization policy | | php zero make:provider {name} | Create a new service provider | | php zero make:command {name} | Create a new console command | | php zero make:test {name} | Create a unit test (--feature for a feature test) |Routes| Command | Description | |---|---| | php zero route:list | Display all registered routes (with names, color-coded methods) | | php zero route:cache | Cache routes for faster resolution | | php zero route:clear | Clear the route cache |Configuration| Command | Description | |---|---| | php zero config:cache | Cache configuration files | | php zero config:clear | Clear the configuration cache |Cache &amp; Views| Command | Description | |---|---| | php zero cache:clear | Flush the application cache | | php zero view:cache | Cache compiled PHP view files | | php zero view:clear | Clear compiled view files |Optimization| Command | Description | |---|---| | php zero optimize | Cache config, routes, views, and search index | | php zero optimize:clear | Clear all cached data |Queue| Command | Description | |---|---| | php zero queue:work | Process jobs on the queue | | php zero queue:listen | Listen to a queue | | php zero queue:failed | List failed queue jobs | | php zero queue:retry {id} | Retry a failed job | | php zero queue:clear | Delete all jobs from the queue | | php zero queue:restart | Restart queue workers |Schedule| Command | Description | |---|---| | php zero schedule:run | Run scheduled commands | | php zero schedule:list | List scheduled commands | | php zero schedule:clear | Clear scheduled commands |Security| Command | Description | |---|---| | php zero key:generate | Generate a new application key | | php zero security:test | Run security diagnostics |Testing| Command | Description | |---|---| | php zero test | Run the framework test suite | | php zero orm:test | Test the ORM | | php zero cache:test | Test the cache system | | php zero mail:test | Test the mail system | | php zero queue:test | Test the queue system | | php zero schedule:test | Test the scheduler | | php zero security:test | Test the security layer | | php zero storage:test | Test the storage system | | php zero log:test | Test the logger | | php zero config:test | Test the config system | | php zero validate:test | Test the validator |',
    'url' => '/docs/cli',
  ),
  24 =>
  array (
    'slug' => 'validation',
    'title' => 'Validation',
    'content' => 'ValidationZeroPing provides a flexible validation system with support for complex rule chains, file uploads, custom messages, and both fluent and declarative APIs.Basic UsageCopy$validator = Validator::make($data, [
    &#039;name&#039;  =&gt; &#039;required|min:2|max:255&#039;,
    &#039;email&#039; =&gt; &#039;required|email|unique:users,email&#039;,
    &#039;age&#039;   =&gt; &#039;required|integer|min:18&#039;,
]);

if ($validator-&gt;fails()) {
    $errors = $validator-&gt;errors();
}Available RulesCore Rules| Rule | Description | |---|---| | required | Field must not be empty | | nullable | Skip validation if field is empty | | string | Value must be a string | | integer | Value must be an integer | | numeric | Value must be numeric | | email | Value must be a valid email | | min:N | Minimum length/value of N | | max:N | Maximum length/value of N | | between:N,M | Value must be between N and M | | same:field | Value must match another field | | confirmed | Value must match field_confirmation |Database Rules| Rule | Description | |---|---| | unique:table,column,except,idColumn | Value must be unique in the database | | exists:table,column | Value must exist in the database |File Rules| Rule | Description | |---|---| | file | Value must be a valid uploaded file | | image | Value must be an image (jpeg, png, gif, webp, svg, bmp) | | mimes:ext,ext,... | File must have one of the given extensions | | size:N | File size must not exceed N kilobytes |Value Rules| Rule | Description | |---|---| | array | Value must be an array | | in:a,b,c | Value must be one of the given values | | not_in:a,b,c | Value must not be any of the given values | | regex:/pattern/ | Value must match the regex pattern |FluentValidatorThe FluentValidator provides a chainable API for defining rules inline:Copyuse App\\Core\\Validation\\FluentValidator;

$v = FluentValidator::make($requestData)
    -&gt;field(&#039;name&#039;)-&gt;required()-&gt;string()-&gt;min(2)-&gt;max(255)-&gt;end()
    -&gt;field(&#039;email&#039;)-&gt;required()-&gt;email()-&gt;end()
    -&gt;field(&#039;avatar&#039;)-&gt;file()-&gt;image()-&gt;size(2048)-&gt;end()
    -&gt;field(&#039;role&#039;)-&gt;in([&#039;admin&#039;, &#039;editor&#039;, &#039;user&#039;])-&gt;end();

if ($v-&gt;fails()) {
    $errors = $v-&gt;errors();
}Available Fluent MethodsCopy$v-&gt;field(&#039;name&#039;)
    -&gt;required($message)
    -&gt;string($message)
    -&gt;email($message)
    -&gt;numeric($message)
    -&gt;integer($message)
    -&gt;min(2, $message)
    -&gt;max(255, $message)
    -&gt;between(1, 10, $message)
    -&gt;array($message)
    -&gt;file($message)
    -&gt;image($message)
    -&gt;mimes(&#039;jpg,png,pdf&#039;, $message)
    -&gt;size(2048, $message)
    -&gt;in([&#039;a&#039;, &#039;b&#039;], $message)
    -&gt;notIn([&#039;x&#039;, &#039;y&#039;], $message)
    -&gt;regex(&#039;/^[a-z]+$/&#039;, $message)
    -&gt;same(&#039;other_field&#039;, $message)
    -&gt;confirmed($message)
    -&gt;unique(&#039;users&#039;, &#039;email&#039;)
    -&gt;exists(&#039;roles&#039;, &#039;id&#039;)
    -&gt;end();FormRequestFor complex validation logic, extend the FormRequest class:Copy&lt;?php

namespace App\\Http\\Requests;

use App\\Core\\Validation\\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Return false to deny the request
        return true;
    }

    public function rules(): array
    {
        return [
            &#039;name&#039;  =&gt; &#039;required|string|min:2|max:255&#039;,
            &#039;email&#039; =&gt; &#039;required|email|unique:users,email&#039;,
            &#039;password&#039; =&gt; &#039;required|min:8|confirmed&#039;,
        ];
    }

    public function messages(): array
    {
        return [
            &#039;name.required&#039; =&gt; &#039;Please provide your name.&#039;,
            &#039;email.unique&#039;  =&gt; &#039;This email is already taken.&#039;,
        ];
    }
}Use it in a controller:Copypublic function store(Request $request)
{
    $formRequest = new StoreUserRequest($request-&gt;all());

    if ($formRequest-&gt;fails()) {
        return redirect()-&gt;back()-&gt;withErrors($formRequest-&gt;errors());
    }

    $validated = $formRequest-&gt;validated();
    // $validated contains only the fields defined in rules()
}FormRequest Methods| Method | Description | |---|---| | rules() | Define validation rules (abstract) | | messages() | Custom error messages per field/rule | | authorize() | Determine if the request is authorized | | validated() | Get validated data (throws ValidationException on failure) | | passes() | Check if validation passed | | fails() | Check if validation failed | | errors() | Get validation errors | | input(key, default) | Get a single input value | | all() | Get all input data |Custom Error MessagesFor the standard Validator, pass messages as a global helper:Copy$validator = Validator::make($data, $rules);Custom messages can be defined in the messages() method of a FormRequest.ValidationExceptionWhen calling validated() on a FormRequest that fails, a ValidationException is thrown:Copytry {
    $validated = $request-&gt;validated();
} catch (ValidationException $e) {
    $errors = $e-&gt;errors();
}',
    'url' => '/docs/validation',
  ),
  25 =>
  array (
    'slug' => 'search',
    'title' => 'Search',
    'content' => 'Documentation SearchZeroPing includes a full-text search engine for your documentation, accessible via the search modal (Ctrl+K / Cmd+K) or the /search API endpoint.Search IndexThe search index is built from all markdown documents in docs/.',
    'url' => '/docs/search',
  ),
  26 =>
  array (
    'slug' => 'search',
    'title' => 'Search',
    'content' => 'Each document is parsed into words, stemmed, and stored with positional data for relevance scoring.Building the IndexCopyphp zero search:indexThis scans all .md files in the docs/ directory, builds a term-frequency map, and writes the index to storage/cache/search.index.Automatic RebuildingThe index is rebuilt automatically when:You run php zero optimizeThe search:index command is executedA cache clear is performed with php zero optimize:clearSearch APIAJAX EndpointCopyGET /search?q=validationReturns JSON:Copy{
    &quot;query&quot;: &quot;validation&quot;,
    &quot;count&quot;: 3,
    &quot;results&quot;: [
        {
            &quot;title&quot;: &quot;Validation&quot;,
            &quot;url&quot;: &quot;/docs/validation&quot;,
            &quot;content&quot;: &quot;…with &lt;mark&gt;validation&lt;/mark&gt; rules…&quot;,
            &quot;score&quot;: 0.95
        }
    ]
}Frontend IntegrationThe search modal in views/layouts/site.php is wired to this endpoint with:Debounced input — 250ms delay before searchingRecent searches — last 5 queries stored in localStorageHighlighted results — matching terms are wrapped in &lt;mark&gt; tagsEmpty state — friendly message when no results matchError state — graceful fallback when the server is unreachableFuzzy MatchingResults that don&#039;t match exactly are ranked using Levenshtein distance.',
    'url' => '/docs/search',
  ),
  27 =>
  array (
    'slug' => 'search',
    'title' => 'Search',
    'content' => 'This means:valdiation still finds &quot;Validation&quot;instal finds &quot;Installation&quot;gettin startd finds &quot;Getting Started&quot;ScoringResults are sorted by:1. Title match (highest weight) 2. Content term frequency 3. Fuzzy distance (lower = better) 4. Document length (shorter = slightly preferred)Only results above the relevance threshold (score &gt; 0.2) are returned.',
    'url' => '/docs/search',
  ),
  28 =>
  array (
    'slug' => 'starter-templates',
    'title' => 'Starter Templates',
    'content' => 'Starter TemplatesScaffold a new ZeroPing project from a pre-built template with php zero new.Copyphp zero new {type} [--name=&quot;My Project&quot;] [--dir=./path]Available Templates`empty`Minimal project skeleton with a single welcome page.Copymy-app/
├── app/Controllers/HomeController.php
├── config/app.php
├── config/database.php
├── config/routes.php
├── public/index.php
├── storage/
└── views/welcome.php`blog`Blog with posts, models, migrations, and full CRUD views.Copyphp zero new blog --name=&quot;My Blog&quot;Includes:Post model with scopes (published, latest)PostController with index and show actionsPosts migration (001_create_posts_table.php)Tailwind-styled views (home, posts index, post show)App layout with navigation`api`RESTful API skeleton with JSON helpers and auth boilerplate.Copyphp zero new api --name=&quot;My API&quot;Includes:AuthController with login endpoint and validationUserController with index and show endpointsJSON response helpersClean route definitions under /api/*`mvc`Full MVC CRUD scaffold with user management.Copyphp zero new mvc --name=&quot;My App&quot;Includes:User model with fillable, hidden, and mutatorsUserController with index, create, storeHomeControllerUsers migration (001_create_users_table.php)Tailwind views (home, users index, create user form)Form validation with error handlingProject ConfigurationWhen using --name, the project name is used to fill in:| Placeholder | Replaced with | |---|---| | {{ project_name }} | The value of --name | | {{ project_slug }} | URL-friendly version of the name | | {{ project_type }} | The template type (e.g., BLOG) |Next Steps After ScaffoldingCopycd my-app
# Configure your environment
cp .env.example .env
# Start the dev server
php zero serve',
    'url' => '/docs/starter-templates',
  ),
  29 =>
  array (
    'slug' => 'performance',
    'title' => 'Performance',
    'content' => 'PerformanceZeroPing includes several performance optimization features to speed up your application in production.Route CachingRoute caching compiles all registered routes into a single optimized file, eliminating the need to parse config/routes.php on every request.Copyphp zero route:cacheTo clear the route cache:Copyphp zero route:clearConfiguration CachingConfiguration caching merges all config files into a single array and writes it to a cached file.',
    'url' => '/docs/performance',
  ),
  30 =>
  array (
    'slug' => 'performance',
    'title' => 'Performance',
    'content' => 'This avoids repeated require calls and file I/O on every request.Copyphp zero config:cacheTo clear the config cache:Copyphp zero config:clearView CachingCompiled views are stored in storage/cache/views/.',
    'url' => '/docs/performance',
  ),
  31 =>
  array (
    'slug' => 'performance',
    'title' => 'Performance',
    'content' => 'Once cached, views are served directly from the pre-compiled file without re-parsing the template.Copyphp zero view:cacheTo clear the view cache:Copyphp zero view:clearOptimize CommandThe optimize command runs all caching operations at once:Copyphp zero optimizeThis runs:1. config:cache — Cache configuration 2. route:cache — Cache routes 3. view:cache — Cache compiled views 4. search:index — Build search indexTo clear everything:Copyphp zero optimize:clearLazy Service LoadingServices are loaded lazily through the container.',
    'url' => '/docs/performance',
  ),
  32 =>
  array (
    'slug' => 'performance',
    'title' => 'Performance',
    'content' => 'A service provider is only instantiated and registered when one of its services is first requested. This means:Unused services consume zero memoryRequest time is not spent booting providers that won&#039;t be usedOnly the core routing and config systems are loaded eagerlyBest Practices1. Run php zero optimize in deployment for maximum performance 2. Keep config files minimal — only load what you need 3. Use route caching in production environments 4.',
    'url' => '/docs/performance',
  ),
  33 =>
  array (
    'slug' => 'performance',
    'title' => 'Performance',
    'content' => 'Cache views for any pages rendered more than once 5. Build the search index on deploy, not on first user request',
    'url' => '/docs/performance',
  ),
  34 =>
  array (
    'slug' => 'roadmap',
    'title' => 'Roadmap',
    'content' => 'RoadmapZeroPing is actively evolving with a focus on stability first, then ecosystem growth.v1.1 ✅Documentation search indexing with fuzzy matching, AJAX endpoint, and local recent searchesStarter templates (php zero new) for blog, API, MVC, and empty project patternsExpanded CLI ergonomics (new, search:index commands)Enhanced validation (array, file, image, mimes, size, in, not_in, regex rules, FluentValidator, FormRequest)Performance optimizations (view caching, route caching, config caching, lazy service loading)v1.2Package ecosystem and plugin hooksDeployment blueprintsImproved observability and debug toolingLong-term directionFirst-party cloud workflowsTeam-oriented DX featuresAdvanced code generation pipelines',
    'url' => '/docs/roadmap',
  ),
);
