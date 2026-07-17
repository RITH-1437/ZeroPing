# Examples & Demo Applications Plan

This document outlines the example applications we want to provide so external
developers can learn ZeroPing by reading real, runnable code. It is a **plan**,
not a commitment to ship all of them at once — each should be added only when
it clearly improves adoption and stays within ZeroPing's "lightweight, zero
magic" philosophy.

## Already available (starter templates)

Scaffold any of these with `php zero new {type}`:

| Template | What it demonstrates |
|---|---|
| `empty` | Minimal skeleton with a single welcome page |
| `mvc` | Full CRUD with user management |
| `blog` | Posts with pagination and categories |
| `api` | RESTful JSON API with authentication boilerplate |
| `dashboard` | Admin dashboard with summary widgets |

## Proposed future examples

| Example | Priority | Demonstrates |
|---|---|---|
| **Auth demo** | High | Registration, login/logout, password hashing, route middleware, session guards |
| **Task manager** | High | CRUD + relationships, validation, flash messages, pagination |
| **Portfolio / marketing site** | Medium | Static-ish routing, layouts, view composition, caching |
| **REST API service** | Medium | Stateless auth (token), JSON requests/responses, validation, rate limiting |
| **E-commerce (minimal)** | Low | Products, cart, orders, transactions, queues for order processing |
| **Real-time notifications** | Low | Queue workers + a lightweight polling/SSE endpoint |

## Guidelines for every example

- Keep each example self-contained and runnable with `composer install`,
  `cp .env.example .env`, `php zero key:generate`, `php zero migrate`,
  `php zero serve`.
- Prefer the framework's built-in features over third-party packages.
- Include a short `README.md` inside the example explaining what it teaches.
- Add at least one test so the example itself is verifiable.
- Document the example in `docs/website/starter-templates.md` once published.

## How examples are delivered

Starter templates live under `templates/` at the repository root and are copied
by the `php zero new` command. Larger demos that are not templates may instead be
linked from the documentation as standalone repositories to avoid bloating the
core package.
