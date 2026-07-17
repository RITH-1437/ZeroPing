# Getting Started

Build your first page by defining a route, controller action, and view.

## Define the route

```php
use App\Core\Routing\Router;
use App\Controllers\HomeController;

Router::get('/', [HomeController::class, 'index']);
```

## Create a controller action

```php
public function home(): void
{
    $this->view('site/home', ['title' => 'ZeroPing']);
}
```

## Create a view

```php
<h1>Welcome to ZeroPing</h1>
<p>You are ready to build.</p>
```

## Add styling with Tailwind

Use Tailwind utility classes to ship responsive layouts quickly while keeping design consistent.
