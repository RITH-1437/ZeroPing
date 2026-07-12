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
    'content' => 'InstallationInstall ZeroPing with Composer and run migrations.RequirementsPHP 8.1+ComposerMySQL-compatible databaseClone the repositoryCopygit clone https://github.com/RITH-1437/ZeroPing.git
cd ZeroPingInstall dependenciesCopycomposer installEnvironment configurationCopycp .env.example .envUpdate your database credentials in .env, then run:Copyphp cli\\migrate.phpServe the applicationCopyphp -S localhost:1437 -t public',
    'url' => '/docs/installation',
  ),
  2 =>
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
  3 =>
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
  4 =>
  array (
    'slug' => 'cli',
    'title' => 'CLI Reference',
    'content' => 'CLI ReferenceZeroPing ships with a powerful CLI (php zero) for managing your application.General Commands| Command | Description | |---|---| | php zero | Display the command list | | php zero about | Display framework information | | php zero version | Show the framework version | | php zero serve | Start the development server |Project Scaffolding| Command | Description | |---|---| | php zero new {type} | Create a new project from a template |Types: empty, blog, api, mvcCopyphp zero new blog --name=&quot;My Blog&quot; --dir=./my-blogSearch| Command | Description | |---|---| | php zero search:index | Build the documentation search index |Migrations| Command | Description | |---|---| | php zero migrate | Run all pending migrations | | php zero migrate:fresh | Drop all tables and re-run migrations | | php zero migrate:refresh | Rollback all migrations then re-run them | | php zero migrate:rollback | Rollback the last migration | | php zero migrate:status | Show migration status |Make (Code Generation)| Command | Description | |---|---| | php zero make:model {name} | Create a new model | | php zero make:controller {name} | Create a new controller | | php zero make:service {name} | Create a new service class | | php zero make:repository {name} | Create a new repository | | php zero make:migration {name} | Create a new migration | | php zero make:mail {name} | Create a new mailable class |Routes| Command | Description | |---|---| | php zero route:list | Display all registered routes | | php zero route:cache | Cache routes for faster resolution | | php zero route:clear | Clear the route cache |Configuration| Command | Description | |---|---| | php zero config:cache | Cache configuration files | | php zero config:clear | Clear the configuration cache |Cache| Command | Description | |---|---| | php zero cache:clear | Flush the application cache | | php zero view:cache | Cache compiled view files | | php zero view:clear | Clear compiled view files |Optimization| Command | Description | |---|---| | php zero optimize | Cache config, routes, views, and search index | | php zero optimize:clear | Clear all cached data |Queue| Command | Description | |---|---| | php zero queue:work | Process jobs on the queue | | php zero queue:listen | Listen to a queue | | php zero queue:failed | List failed queue jobs | | php zero queue:retry {id} | Retry a failed job | | php zero queue:clear | Delete all jobs from the queue | | php zero queue:restart | Restart queue workers |Schedule| Command | Description | |---|---| | php zero schedule:run | Run scheduled commands | | php zero schedule:list | List scheduled commands | | php zero schedule:clear | Clear scheduled commands |Security| Command | Description | |---|---| | php zero key:generate | Generate a new application key | | php zero security:test | Run security diagnostics |Testing| Command | Description | |---|---| | php zero test | Run framework tests | | php zero orm:test | Test ORM | | php zero cache:test | Test cache system | | php zero mail:test | Test mail system | | php zero queue:test | Test queue system | | php zero schedule:test | Test scheduler | | php zero security:test | Test security layer | | php zero storage:test | Test storage system | | php zero log:test | Test logger | | php zero config:test | Test config system | | php zero validate:test | Test validator |',
    'url' => '/docs/cli',
  ),
  5 =>
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
  6 =>
  array (
    'slug' => 'search',
    'title' => 'Search',
    'content' => 'Documentation SearchZeroPing includes a full-text search engine for your documentation, accessible via the search modal (Ctrl+K / Cmd+K) or the /search API endpoint.Search IndexThe search index is built from all markdown documents in docs/.',
    'url' => '/docs/search',
  ),
  7 =>
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
  8 =>
  array (
    'slug' => 'search',
    'title' => 'Search',
    'content' => 'This means:valdiation still finds &quot;Validation&quot;instal finds &quot;Installation&quot;gettin startd finds &quot;Getting Started&quot;ScoringResults are sorted by:1. Title match (highest weight) 2. Content term frequency 3. Fuzzy distance (lower = better) 4. Document length (shorter = slightly preferred)Only results above the relevance threshold (score &gt; 0.2) are returned.',
    'url' => '/docs/search',
  ),
  9 =>
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
  10 =>
  array (
    'slug' => 'performance',
    'title' => 'Performance',
    'content' => 'PerformanceZeroPing includes several performance optimization features to speed up your application in production.Route CachingRoute caching compiles all registered routes into a single optimized file, eliminating the need to parse config/routes.php on every request.Copyphp zero route:cacheTo clear the route cache:Copyphp zero route:clearConfiguration CachingConfiguration caching merges all config files into a single array and writes it to a cached file.',
    'url' => '/docs/performance',
  ),
  11 =>
  array (
    'slug' => 'performance',
    'title' => 'Performance',
    'content' => 'This avoids repeated require calls and file I/O on every request.Copyphp zero config:cacheTo clear the config cache:Copyphp zero config:clearView CachingCompiled views are stored in storage/cache/views/.',
    'url' => '/docs/performance',
  ),
  12 =>
  array (
    'slug' => 'performance',
    'title' => 'Performance',
    'content' => 'Once cached, views are served directly from the pre-compiled file without re-parsing the template.Copyphp zero view:cacheTo clear the view cache:Copyphp zero view:clearOptimize CommandThe optimize command runs all caching operations at once:Copyphp zero optimizeThis runs:1. config:cache — Cache configuration 2. route:cache — Cache routes 3. view:cache — Cache compiled views 4. search:index — Build search indexTo clear everything:Copyphp zero optimize:clearLazy Service LoadingServices are loaded lazily through the container.',
    'url' => '/docs/performance',
  ),
  13 =>
  array (
    'slug' => 'performance',
    'title' => 'Performance',
    'content' => 'A service provider is only instantiated and registered when one of its services is first requested. This means:Unused services consume zero memoryRequest time is not spent booting providers that won&#039;t be usedOnly the core routing and config systems are loaded eagerlyBest Practices1. Run php zero optimize in deployment for maximum performance 2. Keep config files minimal — only load what you need 3. Use route caching in production environments 4.',
    'url' => '/docs/performance',
  ),
  14 =>
  array (
    'slug' => 'performance',
    'title' => 'Performance',
    'content' => 'Cache views for any pages rendered more than once 5. Build the search index on deploy, not on first user request',
    'url' => '/docs/performance',
  ),
  15 =>
  array (
    'slug' => 'roadmap',
    'title' => 'Roadmap',
    'content' => 'RoadmapZeroPing is actively evolving with a focus on stability first, then ecosystem growth.v1.1 ✅Documentation search indexing with fuzzy matching, AJAX endpoint, and local recent searchesStarter templates (php zero new) for blog, API, MVC, and empty project patternsExpanded CLI ergonomics (new, search:index commands)Enhanced validation (array, file, image, mimes, size, in, not_in, regex rules, FluentValidator, FormRequest)Performance optimizations (view caching, route caching, config caching, lazy service loading)v1.2Package ecosystem and plugin hooksDeployment blueprintsImproved observability and debug toolingLong-term directionFirst-party cloud workflowsTeam-oriented DX featuresAdvanced code generation pipelines',
    'url' => '/docs/roadmap',
  ),
);
