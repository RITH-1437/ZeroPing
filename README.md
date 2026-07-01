# 🎮 ZeroPing

> A modern Gaming Café Reservation System built with a custom PHP MVC Framework.

![PHP](https://img.shields.io/badge/PHP-8.0+-777BB4?style=for-the-badge&logo=php)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql)
![AJAX](https://img.shields.io/badge/AJAX-JavaScript-yellow?style=for-the-badge)
![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)

---

# 📖 About

ZeroPing is a web application that allows users to discover nearby gaming cafés, reserve gaming PCs, make online payments, and manage reservations.

This project is developed using a **custom PHP MVC framework** without Laravel or other PHP frameworks to demonstrate backend architecture and software engineering concepts.

---

# ✨ Features

## 👤 Authentication

- User Registration
- User Login
- Secure Password Hashing
- Session Authentication
- Role-based Access Control

---

## 🏢 Gaming Café

- Browse Gaming Cafés
- View Café Details
- Search Cafés
- Nearby Cafés
- Favorite Cafés

---

## 💻 Gaming PCs

- PC Availability
- PC Specifications
- PC Status
- Real-time Reservation

---

## 📅 Reservation

- Reserve Gaming PCs
- Select Date & Time
- Reservation History
- Cancel Reservation

---

## 💳 Payment

- ABA KHQR
- Wing
- ACLEDA
- Cash Payment

---

## 🗺️ Google Maps

- Nearby Gaming Cafés
- GPS Location
- Distance Calculation
- Navigation

---

## 🔔 Notifications

- Reservation Reminder
- Booking Confirmation
- Payment Status

---

# 🏗️ Tech Stack

| Technology | Usage |
|------------|-------|
| PHP | Backend |
| MySQL | Database |
| AJAX | Async Requests |
| JavaScript | Frontend |
| HTML5 | Structure |
| CSS3 | Styling |
| Composer | Dependency Management |

---

# 📂 Project Structure

```text
zero-ping/

├── app/
│   ├── controllers/
│   ├── core/
│   ├── helpers/
│   ├── middleware/
│   ├── models/
│   └── services/
│
├── config/
│
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── backups/
│
├── public/
│   ├── assets/
│   └── index.php
│
├── storage/
│
├── vendor/
│
├── views/
│
├── .env
├── composer.json
└── README.md
```

---

# 🚀 Installation

## Clone Project

```bash
git clone https://github.com/yourusername/zero-ping.git
```

---

## Install Dependencies

```bash
composer install
```

---

## Configure Environment

Create a `.env` file.

```env
DB_HOST=localhost
DB_NAME=zero_ping
DB_USER=root
DB_PASS=
DB_CHARSET=utf8mb4
```

---

## Run Migrations

```bash
php cli/migrate.php
```

---

## Start Development Server

```bash
php -S localhost:1437 -t public
```

---

Open your browser.

```
http://localhost:1437
```

---

# 🏛️ Framework Features

- MVC Architecture
- Custom Router
- Middleware
- Session Management
- Authentication
- Migration System
- Base Model (CRUD)
- Service Layer
- Environment Configuration

---

# 📌 Development Progress

- [x] MVC Architecture
- [x] Router
- [x] Autoloader
- [x] Database Connection
- [x] Migration System
- [x] Authentication
- [x] User Registration
- [x] User Login
- [ ] Layout System
- [ ] Flash Messages
- [ ] Validation
- [ ] Gaming Café Module
- [ ] Reservation Module
- [ ] Payment Module
- [ ] Google Maps Integration
- [ ] Admin Dashboard

---

# 👨‍💻 Author

**Rin Nairith**

Full Stack Developer Student

---

# 📄 License

This project is licensed under the MIT License.