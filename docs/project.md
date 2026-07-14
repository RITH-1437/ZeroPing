# ZeroPing Framework

## Phase 8 — Complete ORM

### Completed Features

- Mass Assignment
- Automatic Timestamps
- Attribute Casting
- Relationships (HasOne, HasMany, BelongsTo, BelongsToMany)
- Lazy Loading
- Query Scopes
- Soft Deletes
- Model Events
- Accessors & Mutators
- Pagination
- Collections
- Query Builder Improvements
- Model Utilities
- Custom Exceptions
- ORM Testing Command

### Architecture

The ORM is designed to be a modern Active Record implementation. It is built on top of the existing `QueryBuilder` and follows SOLID principles. The core components of the ORM are:

- **Model:** The base class for all models. It provides the Active Record API.
- **Builder:** The query builder for the ORM. It provides a fluent interface for building queries.
- **Collection:** A custom collection class that provides a fluent interface for working with arrays of data.
- **Hydrator:** Responsible for creating model instances from database results.
- **Persister:** Responsible for saving and updating models in the database.
- **Concerns:** A set of traits that provide additional functionality to models, such as mass assignment, timestamps, and soft deletes.
- **Relations:** A set of classes that define the relationships between models.
- **Exceptions:** A set of custom exceptions for the ORM.

### ORM Folder Structure

```
Core
└── ORM
    ├── Builder.php
    ├── Collection.php
    ├── Hydrator.php
    ├── Model.php
    ├── Persister.php
    ├── Pagination
    │   └── Paginator.php
    ├── Relations
    │   ├── Relation.php
    │   ├── HasOne.php
    │   ├── HasMany.php
    │   ├── BelongsTo.php
    │   └── BelongsToMany.php
    ├── Concerns
    │   ├── HasAttributes.php
    │   ├── GuardsAttributes.php
    │   ├── HasRelationships.php
    │   └── HasTimestamps.php
    └── Exceptions
        ├── ModelNotFoundException.php
        ├── MassAssignmentException.php
        └── RelationNotFoundException.php
```

### Example Code

```php
// Get all users
User::all();

// Find a user by ID
User::find(1);

// Find a user by email
User::where('email', 'admin@example.com')->first();

// Create a new user
User::create([
    'name' => 'John Doe',
    'email' => 'john.doe@example.com',
    'password' => 'password',
]);

// Update a user
$user = User::find(1);
$user->update([
    'name' => 'John Smith',
]);

// Delete a user
$user = User::find(1);
$user->delete();

// Get a user's coffees
$user = User::find(1);
$user->coffees;

// Paginate users
User::latest()->paginate(10);

// Get all users, including soft-deleted users
User::withTrashed()->get();

// Find a user by ID or throw an exception
User::findOrFail(1);
```

## Phase 9 — Cache System

### Completed Features

- Cache Driver Interface
- File Cache Driver
- Array Cache Driver
- Null Cache Driver
- Cache Repository
- Cache Manager
- Cache Facade
- Helper
- Service Provider
- CLI Commands
- Configuration
- Logging

### Architecture

The Cache System is designed to be a modular and extensible system that supports multiple drivers. The core components of the Cache System are:

- **CacheManager:** The main class for managing cache stores.
- **CacheRepository:** The main API for interacting with the cache.
- **CacheDriver:** The interface for all cache drivers.
- **Drivers:** The cache drivers (File, Array, Null).
- **Cache:** The facade for the cache system.
- **cache():** The helper function for the cache system.
- **CacheServiceProvider:** The service provider for the cache system.

### Cache Folder Structure

```
app/Core/Cache
├── Cache.php
├── CacheManager.php
├── CacheRepository.php
├── Drivers
│   ├── CacheDriver.php
│   ├── FileCacheDriver.php
│   ├── ArrayCacheDriver.php
│   └── NullCacheDriver.php
└── Exceptions
    └── CacheException.php
```

### Example Code

```php
// Put an item in the cache
Cache::put('user', $user, 3600);

// Get an item from the cache
$user = Cache::get('user');

// Remember an item in the cache
Cache::remember('users', 300, function () {
    return User::all();
});

// Check if an item exists in the cache
Cache::has('settings');

// Remove an item from the cache
Cache::forget('settings');

// Remove all items from the cache
Cache::flush();
```

## Phase 10 — Filesystem

### Completed Features

- Filesystem Driver Interface
- Local Driver
- Null Driver
- Storage Facade
- Helper
- File Upload
- HTTP Integration
- Configuration
- Service Provider
- CLI

### Architecture

The Filesystem component is designed to be a modern filesystem abstraction layer. It is built to be extensible for cloud storage providers. The core components of the Filesystem are:

- **FilesystemManager:** The main class for managing filesystem disks.
- **FilesystemRepository:** The main API for interacting with the filesystem.
- **FilesystemDriver:** The interface for all filesystem drivers.
- **Drivers:** The filesystem drivers (Local, Null).
- **Storage:** The facade for the filesystem.
- **storage(), storage_path(), public_path():** The helper functions for the filesystem.
- **UploadedFile:** A class to represent an uploaded file.
- **File:** A class to represent a file.
- **FilesystemServiceProvider:** The service provider for the filesystem.

### Filesystem Folder Structure

```
app/Core/Filesystem
├── Storage.php
├── FilesystemManager.php
├── FilesystemRepository.php
├── Drivers
│   ├── FilesystemDriver.php
│   ├── LocalDriver.php
│   └── NullDriver.php
├── UploadedFile.php
├── File.php
└── Exceptions
    └── FilesystemException.php
```

### Example Code

```php
// Put a file on the disk
Storage::put('users/avatar.png', $content);

// Get a file from the disk
$contents = Storage::get('users/avatar.png');

// Check if a file exists
Storage::exists('users/avatar.png');

// Copy a file
Storage::copy('a.txt', 'backup/a.txt');

// Move a file
Storage::move('old.txt', 'new.txt');

// Delete a file
Storage::delete('users/avatar.png');

// Get all files in a directory
$files = Storage::files('users');

// Get all directories in a directory
$directories = Storage::directories();

// Store an uploaded file
$request->file('avatar')->store('avatars');
```

## Phase 11 — Mail System

### Completed Features

- Mail Driver Interface
- SMTP Driver
- Log Driver
- Array Driver
- Null Driver
- Mail Manager
- Mail Repository
- Mail Facade
- Mailable
- Templates
- Attachments
- Configuration
- Service Provider
- Console Commands
- Events
- Logging

### Architecture

The Mail System is designed to be a modern mail system that is extensible for multiple mail drivers. The core components of the Mail System are:

- **MailManager:** The main class for managing mailers.
- **MailRepository:** The main API for interacting with the mail system.
- **MailDriver:** The interface for all mail drivers.
- **Drivers:** The mail drivers (SMTP, Log, Array, Null).
- **Mail:** The facade for the mail system.
- **Mailable:** The base class for all mailable classes.
- **MailServiceProvider:** The service provider for the mail system.

### Mail Folder Structure

```
app/Core/Mail
├── Mail.php
├── MailManager.php
├── MailRepository.php
├── MailMessage.php
├── Mailable.php
├── Mailer.php
├── Address.php
├── Attachment.php
├── Drivers
│   ├── MailDriver.php
│   ├── SMTPDriver.php
│   ├── LogDriver.php
│   ├── ArrayDriver.php
│   └── NullDriver.php
├── Exceptions
│   └── MailException.php
└── Templates
```

### Example Code

```php
// Send a mailable
Mail::to('user@example.com')->send(new WelcomeMail());

// Send a raw text email
Mail::raw('Hello World', function ($message) {
    $message->to('user@example.com')->subject('Hello');
});

// Send an email with an attachment
Mail::to('user@example.com')
    ->attach(storage_path('reports/report.pdf'))
    ->send(new ReportMail());
```

## Phase 12 — Queue System

### Completed Features

- Queue Driver Interface
- Database Driver
- Sync Driver
- Array Driver
- Null Driver
- Job
- Dispatcher
- Queue Worker
- Database
- Mail Integration
- Events
- Logging
- Configuration
- CLI

### Architecture

The Queue System is designed to be a modern queue system that is extensible for multiple queue drivers. The core components of the Queue System are:

- **QueueManager:** The main class for managing queue connections.
- **QueueRepository:** The main API for interacting with the queue.
- **QueueDriver:** The interface for all queue drivers.
- **Drivers:** The queue drivers (Database, Sync, Array, Null).
- **Job:** The base class for all jobs.
- **Dispatcher:** The class for dispatching jobs.
- **Worker:** The class for processing jobs.
- **QueueServiceProvider:** The service provider for the queue system.

### Queue Folder Structure

```
app/Core/Queue
├── Queue.php
├── QueueManager.php
├── QueueRepository.php
├── Dispatcher.php
├── Job.php
├── Worker.php
├── FailedJob.php
├── Drivers
│   ├── QueueDriver.php
│   ├── DatabaseDriver.php
│   ├── SyncDriver.php
│   ├── ArrayDriver.php
│   └── NullDriver.php
└── Exceptions
    └── QueueException.php
```

### Example Code

```php
// Dispatch a job
dispatch(new SendWelcomeEmail($user));

// Dispatch a job with a delay
dispatch(new GenerateInvoice())->delay(60);

// Dispatch a job to a specific queue
dispatch(new CleanupLogs())->onQueue('logs');

// Queue a mailable
Mail::to($user)->queue(new WelcomeMail());
```

## Phase 13 — Task Scheduler

### Completed Features

- Scheduling Frequencies
- Task Types
- Conditional Execution
- Concurrency
- CLI
- Configuration
- Queue Integration
- Logging
- Events
- Mutex

### Architecture

The Task Scheduler is designed to be a modern task scheduler that is extensible for multiple scheduling drivers. The core components of the Task Scheduler are:

- **ScheduleManager:** The main class for managing the schedule.
- **Scheduler:** The class for running the scheduled events.
- **Schedule:** The class for defining the scheduled events.
- **Event:** The base class for all scheduled events.
- **Frequency:** A trait for defining the frequency of scheduled events.
- **Mutex:** An interface for preventing overlapping tasks.
- **ScheduleServiceProvider:** The service provider for the task scheduler.

### Scheduling Folder Structure

```
app/Core/Scheduling
├── Schedule.php
├── Scheduler.php
├── Event.php
├── CallbackEvent.php
├── CommandEvent.php
├── JobEvent.php
├── ScheduleManager.php
├── Frequency.php
├── Mutex.php
├── CronExpression.php
└── Exceptions
    └── ScheduleException.php
```

### Example Code

```php
// Schedule a command
Schedule::command('cache:clear')->daily();

// Schedule a job
Schedule::job(new CleanupLogsJob())->dailyAt('03:00');

// Schedule a callback
Schedule::call(function () {
    Log::info('Maintenance');
})->hourly();
```

## Phase 14 — Security

### Completed Features

- CSRF Protection
- Password Hashing
- Encryption
- Signed URLs
- Rate Limiter
- Random Generators
- Secure Tokens
- Middleware
- Configuration
- Console Commands
- Logging

### Architecture

The Security component is designed to be a modern security layer that protects against common web vulnerabilities. The core components of the Security component are:

- **CSRF:** A class for generating and validating CSRF tokens.
- **Encryption:** A class for encrypting and decrypting data.
- **Hash:** A class for hashing passwords.
- **RateLimiter:** A class for rate limiting requests.
- **Signature:** A class for signing and validating URLs.
- **Middleware:** A set of middleware for protecting against common web vulnerabilities.
- **SecurityServiceProvider:** The service provider for the security component.

### Security Folder Structure

```
app/Core/Security
├── CSRF.php
├── CSRFToken.php
├── Encryption.php
├── Hash.php
├── RateLimiter.php
├── Signature.php
├── URLSigner.php
├── Random.php
├── Password.php
├── Token.php
├── Middleware
│   ├── VerifyCsrfToken.php
│   ├── ThrottleRequests.php
│   └── SignedUrlMiddleware.php
└── Exceptions
    └── SecurityException.php
```

### Example Code

```php
// Generate a CSRF token
$token = CSRF::generate();

// Check a CSRF token
CSRF::check($token);

// Hash a password
$hash = Hash::make('password');

// Check a password
Hash::check('password', $hash);

// Encrypt a value
$encrypted = Encryption::encrypt('hello world');

// Decrypt a value
$decrypted = Encryption::decrypt($encrypted);

// Rate limit a request
RateLimiter::attempt('test', 5);

// Sign a URL
$signedUrl = Signature::sign('http://localhost/test');

// Validate a signed URL
Signature::validate($signedUrl);
```

## Phase 15 — Testing Framework

### Completed Features

- Assertions
- HTTP Testing
- Database Testing
- ORM Testing
- Console Testing
- CLI
- Test Discovery

### Architecture

The Testing Framework is designed to be a lightweight testing framework that is tightly integrated with ZeroPing. The core components of the Testing Framework are:

- **TestCase:** The base class for all tests.
- **TestRunner:** The class for running tests.
- **TestSuite:** The class for defining a test suite.
- **Assertion:** A trait for making assertions.
- **Expect:** A class for making expectations.
- **HTTP:** A set of classes for testing HTTP requests and responses.
- **Database:** A set of classes for testing database interactions.
- **ORM:** A set of classes for testing ORM interactions.
- **Console:** A set of classes for testing console commands.
- **Traits:** A set of traits for interacting with the database, making HTTP requests, and using factories.

### Testing Folder Structure

```
app/Core/Testing
├── TestCase.php
├── TestRunner.php
├── TestSuite.php
├── Assertion.php
├── Expect.php
├── HTTP
│   ├── TestRequest.php
│   ├── TestResponse.php
│   └── HTTPAssertions.php
├── Database
│   ├── RefreshDatabase.php
│   ├── DatabaseAssertions.php
│   └── Transaction.php
├── ORM
│   └── ORMAssertions.php
├── Console
│   └── ConsoleAssertions.php
├── Traits
│   ├── InteractsWithDatabase.php
│   ├── MakesHTTPRequests.php
│   └── UsesFactories.php
└── Exceptions
```

### Example Code

```php
// HTTP Test
$response = $this->get('/');
$response->assertStatus(200);

// Database Test
$this->assertDatabaseHas('users', [
    'email' => 'john.doe@example.com',
]);

// ORM Test
$user = User::factory()->create();
$this->assertModelExists($user);
```

## Phase 16 — Developer Experience (DX)

### Completed Features

- Pretty Exception Page
- Debug Toolbar
- Performance
- Optimize Commands
- Environment
- CLI Improvements
- Generators
- Code Quality
- Debug Helpers

### Architecture

The Developer Experience (DX) features are designed to make ZeroPing enjoyable to use. The core components of the DX features are:

- **DebugBar:** A debug toolbar that displays useful information about the current request.
- **ExceptionHandler:** A class for handling exceptions and rendering pretty error pages.
- **Performance:** A class for measuring performance.
- **Optimize Commands:** A set of commands for optimizing the application.
- **Debug Helpers:** A set of helper functions for debugging.

### Debugging Folder Structure

```
app/Core/Debug
├── DebugBar.php
├── ExceptionHandler.php
├── PrettyException.php
├── Performance.php
├── Stopwatch.php
├── SQLCollector.php
├── RouteCollector.php
├── MemoryCollector.php
├── RequestCollector.php
├── ConfigCollector.php
└── Exceptions
```

### Example Code

```php
// Dump and die
dd($variable);

// Dump a variable
dump($variable);

// Benchmark a function
benchmark(function () {
    // ...
});
```

### Roadmap

- [x] Phase 1: Project Setup
- [x] Phase 2: Routing
- [x] Phase 3: Controllers
- [x] Phase 4: Views
- [x] Phase 5: Database
- [x] Phase 6: Console
- [x] Phase 7: Service Providers
- [x] Phase 8: Complete ORM
- [x] Phase 9: Cache System
- [x] Phase 10: Filesystem
- [x] Phase 11: Mail System
- [x] Phase 12: Queue System
- [x] Phase 13: Task Scheduler
- [x] Phase 14: Security
- [x] Phase 15: Testing Framework
- [x] Phase 16: Developer Experience (DX)
- [ ] Phase 17: Authentication
- [ ] Phase 18: Authorization
- [x] Phase 19: Validation
- [x] Phase 20: Error Handling
- [x] Phase 21: Logging
- [ ] Phase 22: Deployment *(sole remaining item — CI/CD, deploy guides)*
```