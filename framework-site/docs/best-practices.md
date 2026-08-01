# Best Practices

A guide to writing clean, maintainable, and secure ZeroPing applications. These patterns reflect how the framework is designed to be used and are drawn from real-world application structure.

---

## Directory organization

Follow the convention established by the framework generator. Keep each concern in its designated home and avoid cross-layer imports.

```
app/
├── Controllers/        # Thin — receive request, delegate to service, return response
├── Services/           # Business logic — orchestrate repositories and domain objects
├── Repositories/       # Data access — query the database, return domain models
├── Models/             # Database models — schema mapping and relationships only
├── Http/
│   ├── Kernel.php
│   ├── Middleware/     # One class per middleware concern
│   └── Requests/       # FormRequest classes for each form/API input
├── Jobs/               # Queueable units of background work
├── Events/             # Plain data-carrying event objects
├── Listeners/          # Handlers for events
├── Mail/               # Mailable classes
├── Notifications/      # Notification classes
├── Policies/           # Authorization logic
└── Providers/          # Service providers for wiring
```

**Rule of thumb:** if you are writing a `new` statement for a service class inside a controller, move that logic into a service.

---

## Service layer pattern

Controllers should be thin dispatchers. All business logic lives in service classes.

**Bad — fat controller:**

```php
class OrderController
{
    public function store(): Response
    {
        $data = $_POST;

        // Validation
        if (empty($data['product_id'])) {
            return response()->json(['error' => 'product_id required'], 422);
        }

        // Business logic mixed in
        $product = Product::find($data['product_id']);
        if ($product->stock < $data['quantity']) {
            return response()->json(['error' => 'Out of stock'], 422);
        }

        $order = Order::create([...]);
        $product->decrement('stock', $data['quantity']);
        Mail::to(auth()->user()->email)->send(new OrderConfirmation($order));

        return response()->json($order, 201);
    }
}
```

**Good — thin controller + service:**

```php
// app/Http/Requests/CreateOrderRequest.php
class CreateOrderRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'product_id' => 'required|integer|exists:products,id',
            'quantity'   => 'required|integer|min:1',
        ];
    }
}

// app/Services/OrderService.php
class OrderService
{
    public function __construct(
        private OrderRepository   $orders,
        private ProductRepository $products,
        private Mailer            $mailer
    ) {}

    public function place(User $user, array $data): Order
    {
        $product = $this->products->findOrFail($data['product_id']);

        if ($product->stock < $data['quantity']) {
            throw new OutOfStockException($product);
        }

        $order = $this->orders->create($user, $data);
        $this->products->decrementStock($product, $data['quantity']);
        $this->mailer->to($user->email)->send(new OrderConfirmation($order));

        return $order;
    }
}

// app/Controllers/OrderController.php
class OrderController
{
    public function __construct(private OrderService $orders) {}

    public function store(CreateOrderRequest $request): Response
    {
        $order = $this->orders->place(auth()->user(), $request->validated());
        return response()->json($order, 201);
    }
}
```

---

## Repository pattern

Abstract database access behind repository interfaces. This makes services testable (swap real repos for in-memory fakes) and keeps model queries out of controllers.

```php
// app/Contracts/UserRepositoryInterface.php
interface UserRepositoryInterface
{
    public function find(int $id): ?User;
    public function findByEmail(string $email): ?User;
    public function create(array $data): User;
    public function update(User $user, array $data): User;
}

// app/Repositories/UserRepository.php
class UserRepository implements UserRepositoryInterface
{
    public function find(int $id): ?User
    {
        return User::find($id);
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function update(User $user, array $data): User
    {
        $user->fill($data)->save();
        return $user;
    }
}

// app/Providers/AppServiceProvider.php
public function register(): void
{
    $this->container->singleton(
        UserRepositoryInterface::class,
        UserRepository::class
    );
}
```

In tests, swap the repository without touching the database:

```php
$this->container->instance(UserRepositoryInterface::class, new InMemoryUserRepository());
```

---

## FormRequest usage

Use a `FormRequest` for every HTTP action that accepts user input — including API endpoints.

```php
class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Authorization check — return false to get a 403
        return auth()->id() === (int) ($_SERVER['REQUEST_URI_PARAMS']['id'] ?? 0);
    }

    public function rules(): array
    {
        return [
            'name'     => 'required|string|max:100',
            'bio'      => 'nullable|string|max:500',
            'website'  => 'nullable|url',
            'timezone' => 'required|string|in:' . implode(',', timezone_identifiers_list()),
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Your display name is required.',
        ];
    }
}
```

Type-hint the request class in the controller action — the framework resolves, authorizes, and validates before the method runs:

```php
public function update(UpdateProfileRequest $request, int $id): Response
{
    $user = $this->users->update(auth()->user(), $request->validated());
    return response()->json($user);
}
```

Never call `$_POST` or `$_GET` directly in a controller. Use `$request->validated()` or `$request->input()`.

---

## API versioning

Namespace your API routes and controllers by version to allow breaking changes without disrupting existing clients.

```php
// config/routes.php
Router::prefix('/api/v1')->middleware('api')->group(function () {
    require base_path('routes/api_v1.php');
});

Router::prefix('/api/v2')->middleware('api')->group(function () {
    require base_path('routes/api_v2.php');
});
```

Mirror the versioning in your controller namespace:

```
app/Controllers/Api/
├── V1/
│   ├── UserController.php
│   └── PostController.php
└── V2/
    ├── UserController.php    ← new response shape, no breaking changes to V1
    └── PostController.php
```

Use an `Accept: application/vnd.zeroping.v2+json` header strategy as an alternative to URI versioning when you want version-agnostic URLs.

---

## Error handling best practices

### Let exceptions propagate

Do not catch exceptions in services or controllers unless you intend to handle them specifically. Let them bubble to the Kernel's exception handler, which maps them to HTTP responses.

```php
// Good: let the kernel handle unknown exceptions as 500
public function show(int $id): Response
{
    $user = User::findOrFail($id); // throws ModelNotFoundException → 404
    return response()->json($user);
}
```

### Define typed exceptions

Use specific exception classes so the handler can produce accurate HTTP responses:

```php
// app/Exceptions/OutOfStockException.php
class OutOfStockException extends \RuntimeException
{
    public function __construct(Product $product)
    {
        parent::__construct("Product [{$product->id}] is out of stock.", 422);
    }
}
```

Map known exceptions in your `app/Http/Kernel.php` or exception handler:

```php
protected array $knownErrorCodes = [400, 401, 403, 404, 405, 422, 429, 500, 503];
```

### Never expose internals

In production, ensure `APP_DEBUG=false`. The `ErrorRenderer` will display a generic error page rather than a stack trace.

### Log at the right level

```php
logger()->debug('Cache miss', ['key' => $key]);      // dev only
logger()->info('User logged in', ['id' => $user->id]);
logger()->warning('Rate limit approaching', [...]);
logger()->error('Payment gateway timeout', ['order' => $id]);
logger()->critical('Database connection lost', ['dsn' => $dsn]);
```

---

## Security best practices

### CSRF protection

CSRF middleware is included by default in the `web` middleware group. All state-changing forms must include a CSRF token:

```html
<form method="POST" action="/profile">
    <?= csrf_field() ?>
    <!-- ... -->
</form>
```

For SPA/API routes using the `api` middleware group, use stateless token authentication instead of session-based CSRF.

### Mass assignment protection

Always define `$fillable` on models — never set `$guarded = []` in production code:

```php
class User extends Model
{
    // Only these fields may be mass-assigned
    protected array $fillable = ['name', 'email', 'password'];
}
```

### Password hashing

Never store plain-text passwords. Use PHP's `password_hash()` and `password_verify()`:

```php
$user = User::create([
    'email'    => $request->input('email'),
    'password' => password_hash($request->input('password'), PASSWORD_BCRYPT),
]);
```

### SQL injection

The query builder uses parameterized PDO statements throughout. Never concatenate user input into raw SQL:

```php
// Good
User::where('email', $email)->first();
DB::select('SELECT * FROM users WHERE email = ?', [$email]);

// Never do this
DB::raw("SELECT * FROM users WHERE email = '{$email}'");
```

### Output escaping

Escape all user-supplied content before rendering in HTML:

```php
// In a view template
echo htmlspecialchars($user->bio, ENT_QUOTES, 'UTF-8');

// Or using the helper
echo e($user->bio);
```

### Environment secrets

- Store secrets in `.env`, never in source code.
- Never commit `.env` (it is in `.gitignore` by default).
- Use `.env.example` to document required variables without real values.
- In production, set environment variables at the system level, not from a file.

---

## Performance tips

### Use config cache

In production, compile all config files into a single cache file:

```bash
php zero config:cache
```

This eliminates file I/O for config lookups on every request.

### Use route caching

If your route file is large, cache the compiled route list:

```bash
php zero route:cache
```

### Eager load relationships

N+1 queries are the most common performance issue. Use relationship eager loading:

```php
// Bad: 1 query for posts + N queries for users
$posts = Post::all();
foreach ($posts as $post) {
    echo $post->author->name; // N queries
}

// Good: 2 queries total
$posts = Post::with('author')->get();
```

### Cache expensive computations

```php
$stats = cache()->remember('dashboard:stats', 300, function () {
    return [
        'users'  => User::count(),
        'orders' => Order::where('status', 'pending')->count(),
        'revenue'=> Order::sum('total'),
    ];
});
```

### Use the queue for slow operations

Anything that takes more than ~100ms should be queued: email delivery, report generation, image resizing, third-party API calls.

```php
// Instead of blocking the response:
SendWelcomeEmailJob::dispatch($user)->onQueue('mail');
```

### Database indexes

Ensure every column used in a `WHERE`, `ORDER BY`, or `JOIN` clause is indexed:

```php
public function up(): void
{
    Schema::table('posts', function (Blueprint $table) {
        $table->index('user_id');
        $table->index(['status', 'created_at']); // composite
    });
}
```
