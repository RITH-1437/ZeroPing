# ZeroPing Framework Scope

> **Version:** v0.3.0
>
> **Status:** Active Development
>
> ZeroPing is a lightweight PHP MVC framework built from scratch for learning modern framework architecture, software engineering principles, and full-stack application development.

---

# Project Goals

ZeroPing aims to provide a clean, modular, and extensible PHP framework inspired by Laravel and Symfony while maintaining its own architecture and identity.

The framework focuses on:

- MVC Architecture
- Dependency Injection
- Service Providers
- Repository Pattern
- Query Builder
- Database Migrations
- Event System
- CLI Tooling
- Authentication
- Developer Experience

---

# Current Progress

## Core

- [x] Application Bootstrap
- [x] Dependency Injection Container
- [x] Service Providers
- [x] Environment Loader
- [x] Configuration Loading

---

## Routing

- [x] Route Registration
- [x] Route Groups
- [x] Route Prefix
- [x] Route Middleware
- [x] Route Parameters
- [x] Route List CLI

---

## HTTP

- [x] Request
- [x] Response
- [x] Controller
- [x] View Engine
- [x] Layout System

---

## Authentication

- [x] Session Authentication
- [x] Password Hashing
- [x] Login
- [x] Register
- [x] Logout
- [x] Auth Middleware
- [x] Guest Middleware

---

## Database

- [x] PDO Connection
- [x] Base Model
- [x] Query Builder
- [x] Blueprint
- [x] Schema Builder
- [x] Migration Runner
- [x] Migration Commands

---

## Repository Pattern

- [x] Base Repository
- [x] User Repository
- [x] Dependency Injection

---

## Console

- [x] Console Kernel
- [x] Stub System
- [x] Base Command

### Available Commands

- [x] migrate
- [x] make:model
- [x] make:controller
- [x] make:service
- [x] make:repository
- [x] make:migration
- [x] route:list
- [x] log:test

---

## Events

- [x] Event Dispatcher
- [x] Listener
- [x] Event Provider

---

## Logging

- [x] Logger Interface
- [x] File Logger
- [x] Logging Provider

---

# Phase Roadmap

---

# Phase 1 (Completed)

Framework Foundation

- MVC
- Router
- Views
- Controllers
- Environment
- Config
- Request / Response

Status:

Completed

---

# Phase 2 (Completed)

Dependency Injection

- DI Container
- Service Providers
- Repository Pattern

Status:

Completed

---

# Phase 3 (Completed)

Database Layer

- Query Builder
- Model
- Schema Builder
- Migration Runner

Status:

Completed

---

# Phase 4 (Completed)

Console Tooling

- Console
- Command Registry
- Stub Generator
- Make Commands

Status:

Completed

---

# Phase 5 (Completed)

Framework Infrastructure

- Event Dispatcher
- Logging
- Framework Bootstrap

Status:

Completed

---

# Phase 6 (Next)

Configuration Manager

Goal:

Centralize framework configuration.

Features

- config()
- Config Repository
- Config Cache
- Environment Integration

---

# Phase 7

Validation

Features

- Validation Rules
- Custom Rules
- Form Request
- Error Messages

---

# Phase 8

ORM

Features

- Active Record
- Static Model API
- CRUD
- Relationships
- Query Scopes

Example

User::find(1);

User::where('email', $email)->first();

---

# Phase 9

Relationships

Features

- hasOne
- hasMany
- belongsTo
- belongsToMany
- Pivot Tables
- Eager Loading

---

# Phase 10

Cache

Features

- File Cache
- Cache Driver
- Cache Manager
- cache()

---

# Phase 11

Mail

Features

- Mail Manager
- SMTP
- Templates
- Queue Support

---

# Phase 12

Queue

Features

- Job Dispatcher
- Queue Worker
- Delayed Jobs
- Retry System

---

# Phase 13

Filesystem

Features

- Local Storage
- Public Storage
- Upload Manager

---

# Phase 14

Security

Features

- CSRF Protection
- Rate Limiter
- Encryption
- Signed URLs

---

# Phase 15

Testing

Features

- Test Runner
- HTTP Tests
- Unit Tests
- Database Tests

---

# Phase 16

Developer Experience

Features

- Better Error Pages
- Debug Mode
- Profiler
- Route Cache
- Config Cache
- Optimize Command

---

# Long-Term Goals

## CLI

- make:event
- make:listener
- make:middleware
- make:provider
- make:request
- make:policy
- make:job
- make:mail
- make:notification
- serve
- queue:work
- cache:clear
- optimize

---

## Framework Components

- Event System
- Observer
- Notification
- Queue
- Mail
- Cache
- Session
- Cookie
- Scheduler
- Broadcasting
- API Resources

---

# Project Principles

ZeroPing follows these design principles:

- SOLID
- Dependency Injection
- Clean Architecture
- Repository Pattern
- Service Provider Pattern
- Event Driven Architecture
- PSR Standards where practical
- Developer Experience First

---

# Current Version

v0.3.0

Current Focus:

Phase 6 — Configuration Manager

Target:

ZeroPing Framework v1.0