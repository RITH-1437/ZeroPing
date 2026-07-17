# Starter Templates

Scaffold a new ZeroPing project from a pre-built template with `php zero new`.

```bash
php zero new {type} [--name="My Project"] [--dir=./path]
```

## Available Templates

### `empty`

Minimal project skeleton with a single welcome page.

```
my-app/
├── app/Controllers/HomeController.php
├── config/app.php
├── config/database.php
├── config/routes.php
├── public/index.php
├── storage/
└── views/welcome.php
```

### `blog`

Blog with posts, models, migrations, and full CRUD views.

```bash
php zero new blog --name="My Blog"
```

Includes:

- `Post` model with scopes (`published`, `latest`)
- `PostController` with `index` and `show` actions
- Posts migration (`001_create_posts_table.php`)
- Tailwind-styled views (home, posts index, post show)
- App layout with navigation

### `api`

RESTful API skeleton with JSON helpers and auth boilerplate.

```bash
php zero new api --name="My API"
```

Includes:

- `AuthController` with login endpoint and validation
- `UserController` with `index` and `show` endpoints
- JSON response helpers
- Clean route definitions under `/api/*`

### `mvc`

Full MVC CRUD scaffold with user management.

```bash
php zero new mvc --name="My App"
```

Includes:

- `User` model with `fillable`, `hidden`, and mutators
- `UserController` with `index`, `create`, `store`
- `HomeController`
- Users migration (`001_create_users_table.php`)
- Tailwind views (home, users index, create user form)
- Form validation with error handling

## Project Configuration

When using `--name`, the project name is used to fill in:

| Placeholder | Replaced with |
|---|---|
| `{{ project_name }}` | The value of `--name` |
| `{{ project_slug }}` | URL-friendly version of the name |
| `{{ project_type }}` | The template type (e.g., BLOG) |

## Next Steps After Scaffolding

```bash
cd my-app
# Configure your environment
cp .env.example .env
# Start the dev server
php zero serve
```
