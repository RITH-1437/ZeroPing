# Mail

ZeroPing provides a clean mailable API for composing and sending emails. Mail is configured in `config/mail.php` and supports SMTP, log, array (testing), and null drivers. Each email is represented by a **Mailable** class that defines the recipients, subject, and body.

## Configuration

Open `config/mail.php` to configure your mail settings:

```php
return [

    'default' => env('MAIL_DRIVER', 'log'),

    'mailers' => [

        'smtp' => [
            'transport'  => 'smtp',
            'host'       => env('MAIL_HOST', 'localhost'),
            'port'       => env('MAIL_PORT', 587),
            'encryption' => env('MAIL_ENCRYPTION', 'tls'),
            'username'   => env('MAIL_USERNAME'),
            'password'   => env('MAIL_PASSWORD'),
            'timeout'    => null,
        ],

        'log' => [
            'transport' => 'log',
            'channel'   => 'mail',
        ],

        'array' => [
            'transport' => 'array',
        ],

        'null' => [
            'transport' => 'null',
        ],

    ],

    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
        'name'    => env('MAIL_FROM_NAME', 'ZeroPing App'),
    ],

];
```

Set the corresponding values in your `.env` file for SMTP:

```bash
MAIL_DRIVER=smtp
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587
MAIL_ENCRYPTION=tls
MAIL_USERNAME=postmaster@mg.example.com
MAIL_PASSWORD=secret
MAIL_FROM_ADDRESS=no-reply@example.com
MAIL_FROM_NAME="Example App"
```

## Creating a Mailable

```bash
php zero make:mail WelcomeEmail
```

This generates `app/Mail/WelcomeEmailMail.php`. Override `build()` to define the message:

```php
<?php

namespace App\Mail;

use App\Core\Mail\Mailable;

class WelcomeEmail extends Mailable
{
    public function __construct(
        private readonly string $userName,
    ) {}

    public function build(): self
    {
        return $this
            ->subject('Welcome to ZeroPing!')
            ->view('emails.welcome', [
                'name' => $this->userName,
            ]);
    }
}
```

## Mailable Structure

All fluent methods return `$this` so they can be chained:

```php
$mail = (new WelcomeEmail('Ada'))
    ->to('ada@example.com', 'Ada Lovelace')
    ->cc('manager@example.com')
    ->bcc('archive@example.com')
    ->from('noreply@example.com', 'My App')
    ->replyTo('support@example.com')
    ->subject('Welcome!')
    ->view('emails.welcome', ['name' => 'Ada']);
```

To send raw HTML without a view template:

```php
$mail = (new Mailable())
    ->to('user@example.com')
    ->subject('Hello')
    ->html('<h1>Hello World</h1>');
```

To send plain text:

```php
$mail = (new Mailable())
    ->to('user@example.com')
    ->subject('Hello')
    ->text('Hello World');
```

### Attachments

```php
// Attach a file from an absolute path
$mail->attach('/path/to/invoice.pdf', ['as' => 'Invoice.pdf', 'mime' => 'application/pdf']);

// Attach multiple files
$mail->attachMany(['/path/to/a.pdf', '/path/to/b.pdf']);

// Attach from the storage disk
$mail->attachFromStorage('invoices/2024-01.pdf', 'January Invoice.pdf');
```

## Sending Mail

Resolve the `MailManager` from the container and call `send()`:

```php
use App\Core\Mail\MailManager;

$mailer = app(MailManager::class);

$mailer->send(
    (new WelcomeEmail('Ada'))->to('ada@example.com')
);
```

To use a specific mailer (driver):

```php
$mailer->mailer('smtp')->send($mailable);
```

### Sending Raw Text

Use `raw()` when you only need a simple one-off text message with no Mailable class:

```php
$mailer->raw('Your verification code is 123456.', function ($message) {
    $message->to('user@example.com');
    $message->subject('Verification Code');
});
```

## Mail View Templates

Store email view files in `views/emails/`. They are plain PHP templates:

```php
<!DOCTYPE html>
<html>
<body>
    <h1>Welcome, <?= e($name) ?></h1>
    <p>Thanks for joining ZeroPing. You can log in at any time.</p>
</body>
</html>
```

## Available Drivers

| Driver | Description |
|---|---|
| `smtp` | Sends mail via an SMTP server. Use for production. |
| `log` | Writes the mail payload to a log channel. Use for development. |
| `array` | Stores sent messages in memory. Ideal for automated tests. |
| `null` | Discards all messages silently. |

Switch the default driver via the `MAIL_DRIVER` environment variable without changing application code.

## Testing Mail

Set the driver to `array` in your test environment. The `ArrayDriver` stores every sent `Mailable` in memory so you can assert against it:

```php
// In config/mail.php or a test-specific config
'default' => 'array',
```

In your test:

```php
use App\Core\Mail\MailManager;
use App\Core\Mail\Drivers\ArrayDriver;

// Resolve the array driver and inspect stored messages
$manager = app(MailManager::class);
$driver  = $manager->mailer('array')->getDriver();

// Send a message
$manager->mailer('array')->send(
    (new WelcomeEmail('Ada'))->to('ada@example.com')
);

// Assert it was stored
$sent = $driver->messages();
assert(count($sent) === 1);
assert($sent[0]->getSubject() === 'Welcome to ZeroPing!');
```

## Example: Email in a Controller

```php
<?php

namespace App\Controllers\Auth;

use App\Core\Auth\AuthManager;
use App\Core\Mail\MailManager;
use App\Core\View\Controller;
use App\Mail\WelcomeEmail;
use App\Models\User;

class RegisterController extends Controller
{
    public function __construct(
        private readonly MailManager $mailer,
    ) {}

    public function register(): void
    {
        // Validate, create user ...
        $user = User::create([
            'name'     => $_POST['name'],
            'email'    => $_POST['email'],
            'password' => password_hash($_POST['password'], PASSWORD_BCRYPT),
        ]);

        // Send welcome email
        $this->mailer->send(
            (new WelcomeEmail($user['name']))->to($user['email'])
        );

        AuthManager::login($user);
        redirect('/dashboard');
    }
}
```

## Tips

- Use the `log` driver in local development to inspect outgoing mail in your log file without needing a real SMTP server.
- Keep `Mailable::build()` focused on structure and pass data through the constructor. Avoid database queries inside `build()`.
- Always escape user-supplied data in email templates with `e()`.
