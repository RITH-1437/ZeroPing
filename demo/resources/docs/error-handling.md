# Error Handling

## Debug vs Production mode

The `APP_DEBUG` setting in `.env` controls how errors are displayed:

```bash
# .env — development
APP_DEBUG=true

# .env — production
APP_DEBUG=false
```

When `APP_DEBUG` is `true`, ZeroPing renders detailed pretty exception pages with a stack trace, source context, and environment info. In production mode (`false`), a generic error page is shown and the stack trace is hidden.

## Pretty exception pages in development

With debug mode enabled, uncaught exceptions display the `PrettyException` renderer, providing:

- The exception message and class name.
- A highlighted source-code excerpt around the failing line.
- A full call-stack trace.
- Request details (URL, method, and environment).

## Custom error views

Custom HTTP error pages are placed in `views/errors/` and mapped by status code:

```
views/errors/403.php   — Forbidden
views/errors/404.php   — Page Not Found
views/errors/419.php   — Page Expired
views/errors/500.php   — Server Error
```

The framework renders the matching view automatically via `Router::renderError()`. The following variables are available inside each view:

```php
$title       // Error title, e.g. "404 - Page Not Found"
$message     // Exception message
$code        // HTTP status code
$requestUrl  // The requested URI
$debug       // Boolean reflecting APP_DEBUG
```

## ExceptionHandler class

The `App\Core\Debug\ExceptionHandler` class intercepts all uncaught exceptions:

```php
use App\Core\Debug\ExceptionHandler;

$handler = new ExceptionHandler();
$handler->handle($exception);
```

It delegates to `renderPrettyException()` when `config('app.debug')` is `true`, or `renderProductionException()` when false. You may extend the class to customize the logic.

## Logging errors

Errors are logged to `storage/logs/`. The log file is rotated automatically and contains the timestamp, severity level, and message for each entry.

## HTTP status codes from Router

The router's static `renderError()` method sets the response code and loads the appropriate view:

```php
Router::renderError(BASE_PATH, 404, $exception);
Router::renderError(BASE_PATH, 403, $exception);
Router::renderError(BASE_PATH, 500, $exception);
```

Supported status codes are `403`, `404`, `419`, and `500`. If no custom view exists for a given code, the framework falls back to `views/errors/500.php`.
