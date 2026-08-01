# Localization

ZeroPing's localization system lets you store translatable strings in language files and retrieve them anywhere in your application using the `trans()` and `__()` helpers. The `Translator` class handles file loading, locale fallbacks, and `:placeholder` replacement.

## Language File Structure

Language files live in `resources/lang/{locale}/` and return plain PHP arrays:

```txt
resources/
  lang/
    en/
      auth.php
      messages.php
      validation.php
    fr/
      auth.php
      messages.php
    th/
      auth.php
```

Each file returns a flat or nested array of translation strings:

```php
// resources/lang/en/auth.php
return [
    'failed'    => 'These credentials do not match our records.',
    'throttle'  => 'Too many login attempts. Please try again in :seconds seconds.',
    'logout'    => 'You have been logged out.',
];
```

```php
// resources/lang/en/messages.php
return [
    'welcome'  => 'Welcome, :name!',
    'farewell' => 'Goodbye, :name. See you soon.',
    'saved'    => 'Your changes have been saved.',
];
```

## The `trans()` Helper

```php
trans(string $key, array $replace = [], ?string $locale = null): string
```

The `trans()` helper looks up a translation key using dot notation. The first segment is the file name; the rest is the key path within that file.

```php
// Looks up resources/lang/{locale}/auth.php → 'failed'
echo trans('auth.failed');

// With placeholder replacement
echo trans('auth.throttle', ['seconds' => 30]);
// "Too many login attempts. Please try again in 30 seconds."

// Force a specific locale for this call
echo trans('auth.failed', [], 'fr');
```

## The `__()` Helper

`__()` is a direct alias of `trans()` with an identical signature. Use whichever you prefer:

```php
echo __('messages.welcome', ['name' => 'Ada']);
// "Welcome, Ada!"

echo __('auth.logout');
// "You have been logged out."
```

Both helpers are safe to use in views, controllers, and mail templates.

## Locale Configuration

The active locale is configured when the `Translator` is bound in the service container. By default it reads from `config/app.php`:

```php
// config/app.php
return [
    'locale'   => env('APP_LOCALE', 'en'),
    'fallback_locale' => 'en',
];
```

### Changing the Locale at Runtime

Resolve the `Translator` and call `setLocale()`:

```php
use App\Core\Localization\Translator;

$translator = app(Translator::class);
$translator->setLocale('fr');

// All subsequent trans()/__ calls use 'fr'
echo __('auth.failed'); // French string
```

A common pattern is to detect the locale from the session or the `Accept-Language` header in a middleware:

```php
class SetLocaleMiddleware extends Middleware
{
    public function handle(): void
    {
        $locale = session('locale', config('app.locale', 'en'));

        app(Translator::class)->setLocale($locale);
    }
}
```

### Reading the Current Locale

```php
$locale = app(Translator::class)->getLocale(); // e.g. 'fr'
```

## Placeholders

Placeholder names in translation strings are prefixed with `:`. Pass a `$replace` array to substitute them:

```php
// Language file
'greeting' => 'Hello, :name. You have :count new messages.',
```

```php
echo trans('messages.greeting', [
    'name'  => 'Ada',
    'count' => 5,
]);
// "Hello, Ada. You have 5 new messages."
```

Placeholder keys are case-sensitive and replaced literally (no uppercase/title-case variants).

## Nested Keys

Use additional dot segments to access nested arrays:

```php
// resources/lang/en/ui.php
return [
    'buttons' => [
        'save'   => 'Save Changes',
        'cancel' => 'Cancel',
        'delete' => 'Delete',
    ],
];
```

```php
echo trans('ui.buttons.save');   // "Save Changes"
echo trans('ui.buttons.delete'); // "Delete"
```

## Locale Fallback

When a key is not found in the active locale, the `Translator` automatically falls back to the configured `$fallback` locale (default `en`). This means you only need to provide translations for strings that differ from English in each language file.

```php
// Translator constructor
new Translator(
    path:     base_path('resources/lang'),
    locale:   'fr',
    fallback: 'en',          // used when key is missing in 'fr'
);
```

If a key is missing from both the active locale and the fallback, the raw key string is returned (e.g. `'messages.missing_key'`).

## Checking if a Key Exists

```php
$translator = app(Translator::class);

if ($translator->has('auth.failed')) {
    echo trans('auth.failed');
}

// Check in a specific locale
if ($translator->has('auth.failed', 'fr')) {
    echo trans('auth.failed', [], 'fr');
}
```

## Adding a New Language

1. Create the locale directory:

```bash
mkdir -p resources/lang/ja
```

2. Add translation files mirroring the `en/` structure:

```php
// resources/lang/ja/auth.php
return [
    'failed'   => 'ログイン情報が正しくありません。',
    'throttle' => 'ログイン試行回数が多すぎます。:seconds 秒後に再試行してください。',
    'logout'   => 'ログアウトしました。',
];
```

3. Switch to the new locale:

```php
app(Translator::class)->setLocale('ja');
echo trans('auth.failed'); // "ログイン情報が正しくありません。"
```

You only need to provide files for the strings that differ from the fallback. Any key not present in `ja/` will fall back to `en/`.

## Using Translations in Views

```php
<h1><?= e(trans('messages.welcome', ['name' => $user['name']])) ?></h1>
<p><?= e(__('auth.logout')) ?></p>
<button><?= e(trans('ui.buttons.save')) ?></button>
```

## Using Translations in Mailable Classes

```php
public function build(): self
{
    return $this
        ->subject(trans('emails.welcome.subject'))
        ->view('emails.welcome', [
            'greeting' => trans('emails.welcome.greeting', ['name' => $this->name]),
        ]);
}
```

## Tips

- Never echo translation strings without escaping them with `e()` in HTML contexts.
- Keep language files small and focused (one file per feature area). Avoid one giant `messages.php` file.
- Store the user's preferred locale in the session and apply it early in the request lifecycle via middleware.
- Missing keys return the raw key string rather than an empty string, which makes untranslated content obvious during development.
