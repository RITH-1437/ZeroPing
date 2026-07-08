# API

The ZeroPing API surface is intentionally concise and composable.

## Router

```php
Router::get('/users', [UserController::class, 'index']);
Router::post('/users', [UserController::class, 'store']);
```

## Validation

```php
$validator = Validator::make($data, [
    'email' => 'required|email',
    'password' => 'required|min:6',
]);

if ($validator->fails()) {
    // Handle invalid request
}
```

## ORM

```php
$user = User::create([
    'first_name' => 'Rin',
    'last_name' => 'Nairith',
    'email' => 'nairithrin143@gmail.com',
    'password' => PasswordHasher::make('secret123'),
]);
```

## Responses

```php
Response::json(['ok' => true]);
Response::redirect('/dashboard');
```
