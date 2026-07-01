# ZeroPing Framework

Current Version

v0.3.0

## Features

- MVC Architecture
- PSR-4 Autoloading
- Dependency Injection
- Service Layer
- Middleware
- Authentication
- Route Groups
- Route Prefixes
- Dynamic Route Parameters
- Flash Messages
- Validation
- Base Model
- Environment Configuration
---

# 📖 About

ZeroPing is a custom PHP MVC Framework developed for learning modern backend architecture while building the ZeroPing Gaming Cafe Reservation System.

The goal is to understand how frameworks like Laravel work internally instead of relying on them.

---

# ✨ Features

## Core

- MVC Architecture
- Composer PSR-4 Autoloading
- Routing System
- Middleware
- Controllers
- Views with Layouts
- Request Class
- Response Class
- Session Management
- Flash Messages
- Environment Loader (.env)
- Database Connection (PDO)
- Base Model
- Hash Helper
- Validator
- Authentication

---

## Authentication

- User Registration
- User Login
- Logout
- Auth Middleware
- Guest Middleware

---

## Database

- PDO
- Base Model
- CRUD Methods
- Migrations

---

# 📁 Project Structure

```
zero-ping/
│
├── app/
│   ├── Controllers/
│   ├── Core/
│   ├── Middleware/
│   ├── Models/
│   └── Services/
│
├── config/
├── database/
├── public/
├── resources/
├── routes/
├── vendor/
└── views/
```

---

# ⚙️ Requirements

- PHP 8.3+
- Composer
- MySQL

---

# 🚀 Installation

Clone the project

```bash
git clone https://github.com/YOUR_USERNAME/ZeroPing.git
```

Install dependencies

```bash
composer install
```

Generate Composer autoload

```bash
composer dump-autoload
```

Create a `.env` file

```env
APP_NAME=ZeroPing
APP_ENV=development
APP_DEBUG=true

DB_HOST=localhost
DB_NAME=zero_ping
DB_USER=root
DB_PASS=password
DB_CHARSET=utf8mb4
```

Run the application

```bash
php -S localhost:1437 -t public
```

Visit

```
http://localhost:1437
```

---

# ✅ Current Progress

- [x] Composer PSR-4
- [x] Router
- [x] Middleware
- [x] MVC
- [x] Views
- [x] Layouts
- [x] Authentication
- [x] Flash Messages
- [x] Sessions
- [x] Database
- [x] Base Model
- [x] Validator
- [x] Password Hashing

---

# 🛣️ Roadmap

## Framework

- [ ] Dependency Injection Container
- [ ] Route Groups
- [ ] Service Providers
- [ ] Query Builder
- [ ] ORM
- [ ] CSRF Protection
- [ ] Exception Handler
- [ ] CLI Commands
- [ ] Cache
- [ ] Events
- [ ] Queue

## ZeroPing Application

- [ ] Gaming PC Management
- [ ] Reservations
- [ ] Membership
- [ ] Payments
- [ ] QR Code Check-in
- [ ] Admin Dashboard

---

# 👨‍💻 Author

**Rin Nairith**

Built for learning backend development and framework architecture.