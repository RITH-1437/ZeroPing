# Routing

Routes are registered in `config/routes.php` using the static `Router`.

```php
use App\Core\Routing\Router;

Router::get('/', [HomeController::class, 'index']);
Router::get('/users/{id}', fn($id) => "User {$id}");
Router::post('/users', [UserController::class, 'store']);
```

Dynamic segments are wrapped in `{curly_braces}` and are passed to the
handler as ordered arguments. The bundled documentation viewer is wired
as `Router::get('/docs/{page}', ...)`.
