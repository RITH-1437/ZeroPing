# Filesystem

ZeroPing provides a unified filesystem API that works consistently across different storage backends. Disks are configured in `config/filesystem.php` and accessed through the `storage()` helper or the `FilesystemManager` service.

## Configuration

```php
// config/filesystem.php
return [

    'default' => 'local',

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root'   => BASE_PATH . '/storage',
        ],

        'public' => [
            'driver' => 'local',
            'root'   => BASE_PATH . '/storage/public',
            'url'    => '/storage',
        ],

        'cache' => [
            'driver' => 'local',
            'root'   => BASE_PATH . '/storage/cache',
        ],

    ],

];
```

Add disks for any storage location in your application. The `driver` key currently supports `local` and `null` (discard).

## The `storage()` Helper

The global `storage()` helper returns a `FilesystemRepository` for the given disk name. Omit the argument to use the default disk:

```php
// Default disk (local)
$disk = storage();

// Named disk
$disk = storage('public');
$disk = storage('cache');
```

You can also resolve the manager from the container:

```php
use App\Core\Filesystem\FilesystemManager;

$manager = app(FilesystemManager::class);
$disk    = $manager->disk('public');
```

## Reading and Writing Files

### Writing

```php
// Write a string to a file (creates directories automatically)
storage()->put('reports/2024-01.txt', $reportContent);

// Append to a file
storage()->append('logs/app.log', "[2024-01-01] User logged in\n");

// Prepend to a file
storage()->prepend('announcements.txt', "Latest update\n");
```

### Reading

```php
$contents = storage()->get('reports/2024-01.txt');
```

### Copying and Moving

```php
storage()->copy('reports/2024-01.txt', 'reports/backup/2024-01.txt');
storage()->move('reports/draft.txt', 'reports/final.txt');
```

## Checking File Existence

```php
if (storage()->exists('reports/2024-01.txt')) {
    $contents = storage()->get('reports/2024-01.txt');
}
```

## Deleting Files

```php
storage()->delete('reports/draft.txt');
```

## Listing Files and Directories

```php
// List files in a directory (non-recursive)
$files = storage()->files('reports');

// List files recursively
$files = storage()->files('reports', recursive: true);

// List subdirectories
$dirs = storage()->directories('reports');

// Recursively
$dirs = storage()->directories('reports', recursive: true);
```

## File Metadata

```php
$size         = storage()->size('reports/2024-01.txt');     // bytes
$lastModified = storage()->lastModified('uploads/photo.jpg'); // Unix timestamp
$mimeType     = storage()->mimeType('uploads/photo.jpg');   // e.g. "image/jpeg"
```

## The Public Disk and Asset URLs

Files stored on the `public` disk are served directly by the web server. Use `->url()` to generate the public-facing URL:

```php
storage('public')->put('avatars/user-42.jpg', $imageContents);

$url = storage('public')->url('avatars/user-42.jpg');
// Returns: /storage/avatars/user-42.jpg
```

You can also use the `asset()` helper for static assets already in the `public/` directory:

```php
$cssUrl = asset('css/app.css'); // https://example.com/css/app.css
```

## File Visibility

Control file permissions with `getVisibility()` and `setVisibility()`:

```php
storage('public')->setVisibility('docs/report.pdf', 'public');
$visibility = storage('public')->getVisibility('docs/report.pdf'); // 'public' or 'private'
```

## Directories

```php
// Create a directory
storage()->makeDirectory('reports/2024');

// Delete a directory and all its contents
storage()->deleteDirectory('reports/drafts');
```

## Downloading Files

Stream a file as an HTTP download response:

```php
storage()->download('reports/2024-01.pdf', 'January Report.pdf');
```

Optionally pass extra HTTP headers:

```php
storage()->download('reports/2024-01.pdf', 'January Report.pdf', [
    'Cache-Control' => 'no-cache',
]);
```

## Uploading Files

ZeroPing wraps uploaded files in `UploadedFile` objects. Access them from `$_FILES` using the `UploadedFile` class:

```php
use App\Core\Filesystem\UploadedFile;

$file = UploadedFile::fromGlobal('avatar'); // key in $_FILES

if ($file->isValid()) {
    $name = $file->hashName(); // unique filename with original extension
    storage('public')->put("avatars/{$name}", $file->contents());
    $url  = storage('public')->url("avatars/{$name}");
}
```

## Example: File Upload Controller

```php
<?php

namespace App\Controllers;

use App\Core\Auth\AuthManager;
use App\Core\Filesystem\UploadedFile;
use App\Core\View\Controller;

class AvatarController extends Controller
{
    public function update(): void
    {
        $file = UploadedFile::fromGlobal('avatar');

        if (!$file || !$file->isValid()) {
            session(['_errors' => ['avatar' => 'Please choose a valid image file.']]);
            redirect('/account');
            return;
        }

        $allowed = ['image/jpeg', 'image/png', 'image/webp'];

        if (!in_array($file->mimeType(), $allowed, true)) {
            session(['_errors' => ['avatar' => 'Only JPEG, PNG, and WebP images are allowed.']]);
            redirect('/account');
            return;
        }

        if ($file->size() > 2 * 1024 * 1024) { // 2 MB
            session(['_errors' => ['avatar' => 'Image must be smaller than 2 MB.']]);
            redirect('/account');
            return;
        }

        $userId   = AuthManager::id();
        $filename = "avatars/user-{$userId}." . $file->extension();

        storage('public')->put($filename, $file->contents());

        $url = storage('public')->url($filename);

        // Persist $url to the database ...

        redirect('/account');
    }
}
```

## Registering Custom Drivers

Extend the manager with a custom storage backend:

```php
use App\Core\Filesystem\FilesystemManager;

$manager = app(FilesystemManager::class);

$manager->extend('s3', function (array $config): MyS3Driver {
    return new MyS3Driver($config);
});
```

Add the disk in `config/filesystem.php`:

```php
's3' => [
    'driver' => 's3',
    'bucket' => env('S3_BUCKET'),
    'region' => env('S3_REGION'),
    'key'    => env('S3_KEY'),
    'secret' => env('S3_SECRET'),
],
```

## Tips

- Never store user-uploaded files inside `public/` directly; use the `public` storage disk and symlink `storage/public` → `public/storage` so the web server serves the files while ZeroPing controls access.
- Validate MIME type and file size before writing. Do not rely solely on the file extension.
- Use hashed filenames (`hash_file('xxh3', $tmpPath)`) to prevent filename collisions and path traversal attacks.
