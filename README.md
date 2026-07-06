## Framework Roadmap

### v0.1.0
- Basic MVC
- Routing
- Database

### v0.2.0
- Authentication
- Middleware
- Sessions
- Flash Messages

### v0.3.0
- Dependency Injection Container
- Service Providers
- Route Groups
- Dynamic Route Parameters

### v0.5.0
- Event System
- Event Dispatcher
- Logging System
- Console Tooling
- Configuration Manager

### v0.7.0
- Validation Engine
- Rule Registry
- Rule Parser
- Database Presence Verifier
- Validation Rules

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
- Query Builder

---

## Validation

- Required
- Nullable
- String
- Numeric
- Integer
- Email
- Min
- Max
- Between
- Same
- Confirmed
- Unique
- Exists

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
- [x] Event System
- [x] Logging
- [x] Console Commands
- [x] Configuration Manager
- [x] Validation Engine

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