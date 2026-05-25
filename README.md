<div align="center">

# Marketplace Platform

Backend-focused marketplace platform built with Laravel.

Real-time messaging, OAuth authentication, payments, shipping workflow, relational database modeling and production-oriented architecture.

![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-2496ED?style=for-the-badge&logo=docker&logoColor=white)
![PostgreSQL](https://img.shields.io/badge/Database-PostgreSQL-336791?style=for-the-badge&logo=postgresql&logoColor=white)

</div>

---

## Overview

Marketplace Platform is a backend-oriented project designed to simulate a real-world marketplace ecosystem.

The project focuses on demonstrating backend engineering skills using Laravel, including:

- Authentication & Authorization
- OAuth Login
- Role-based access control
- Relational database design
- Real-time communication via WebSockets
- Payment integration
- Shipping workflow
- Queue processing
- API documentation
- Security-oriented architecture

---

## Main Features

### Authentication & Authorization

- User Registration
- Login / Logout
- OAuth Authentication (Google / GitHub)
- Multi-role users
- Spatie Permission roles
- Protected API routes
- Ownership authorization

### Marketplace

- Product management
- Seller & Buyer roles
- Product categories
- Public product listing
- Search & filtering

### Cart & Orders

- Shopping cart
- Checkout workflow
- Orders
- Order items
- Order status lifecycle

### Payments

- Payment gateway integration (Sandbox)
- Webhook processing
- Payment validation

### Shipping

- Shipping simulation
- Shipment tracking
- Shipping status

### Real-Time Messaging

- Buyer ↔ Seller conversations
- WebSockets communication
- Private conversations

### Reviews

- Product ratings
- Purchase validation before review

---

## Tech Stack

### Backend

- Laravel 12
- PHP 8.5+
- REST API
- Laravel Sanctum
- Laravel Socialite
- Spatie Permission
- Laravel Reverb / WebSockets
- Queues
- Docker

### Database

- PostgreSQL / Supabase
- ULID Primary Keys
- Relational Database Modeling
- Soft Deletes
- Indexing strategy

### Integrations

- Google OAuth
- GitHub OAuth
- Payment Gateway (Sandbox)
- Shipping Service (Mock / API)

### Deployment

- Backend: Render / Railway
- Frontend: Vercel

---

## Database Design

This project follows a relational database architecture.

Core entities:

```txt
User
Role
Product
Category
Cart
CartItem
Order
OrderItem
Payment
Shipment
Conversation
Message
Review
```

Database documentation:

```txt
docs/
├── requirements.md
├── database-design.md
├── database-decisions.md
├── database-diagram.mmd
└── database-diagram.svg
```

---

## Security Decisions

This project intentionally applies security-oriented decisions.

Examples:

- ULID primary keys.
- Authorization via ownership validation.
- Protected routes.
- Webhook validation.
- Environment-based secrets.
- Soft deletes for critical entities.

---

## Project Status

Current Stage:

```txt
Planning & Architecture
```

Roadmap:

- [x] Documentation
- [x] Database Modeling
- [x] Laravel Setup
- [ ] Authentication
- [ ] Products
- [ ] Cart
- [ ] Orders
- [ ] Payments
- [ ] Shipping
- [ ] WebSockets Messaging
- [ ] Testing
- [ ] Deployment

---

## Local Setup

Clone repository:

```bash
git clone https://github.com/MarcellusMiller/Marketplace.git
```

Enter project:

```bash
cd marketplace-platform
```

Install dependencies:

```bash
composer install
```

Configure environment:

```bash
cp .env.example .env
php artisan key:generate
```

Run migrations:

```bash
php artisan migrate
```

Start server:

```bash
php artisan serve
```

---

## Future Improvements

- Coupon System
- Refund System
- Notification System
- Audit Logs
- Address Management
- Multi-currency Support
- Mobile Application

---

## Author

### Marcelo Miller

Backend Developer • Software Enthusiast • Always Learning

GitHub:

```txt
https://github.com/MarcellusMiller
```

LinkedIn:

```txt
https://www.linkedin.com/in/marcellusmiller/
```

---


