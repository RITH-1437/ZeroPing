# Introduction

ZeroPing is a modern PHP framework focused on clean architecture, predictable behavior, and a smooth developer experience.

## Core Philosophy

- Keep framework APIs readable and explicit.
- Prefer composable modules over hidden magic.
- Ship practical tooling that supports real production workflows.

## Why ZeroPing

ZeroPing gives teams a lightweight but capable foundation:

- Routing, middleware, and controllers
- ORM with models and relationships
- Queue, cache, mail, and scheduling subsystems
- Built-in CLI commands for common development tasks

## Typical Project Structure

```txt
app/
  Controllers/
  Models/
  Services/
config/
views/
public/
```

## Quick Start

```bash
# Option 1: Zero CLI (fastest)
php zero new my-app
cd my-app
php zero serve

# Option 2: Composer
composer create-project rith-1437/zeroping my-app
cd my-app
php zero serve
```

Both methods generate the same project structure. See [Installation](installation.md) for details.

## Next Step

Continue to the installation guide to bootstrap your first ZeroPing app.
