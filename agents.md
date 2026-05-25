# Marketplace Platform — AI Development Guidelines

## Project Overview

Backend-first marketplace platform built with Laravel.

The project demonstrates:

- Laravel backend architecture
- OAuth authentication
- Authorization
- WebSockets
- Payments
- Shipping workflows
- Relational database modeling
- Production-oriented practices

---

## Tech Stack

### Backend

- Laravel 12
- PHP 8.5+
- PostgreSQL (Supabase)
- Redis
- Sanctum
- Socialite
- Spatie Permission
- Laravel Reverb
- Pest
- Docker

### Frontend

- Vercel deployed frontend
- Backend remains API-first.

---

## Architecture Rules

Controllers must remain thin.

Preferred structure:

```txt
Controllers
↓
Actions / Services
↓
Models
```

Business rules should not live inside controllers.

Prefer:

- Services
- Policies
- Enums
- DTOs when appropriate

---

## Database Rules

### Primary Keys

Use ULID.

### Money Fields

Store monetary values in cents.

Example:

```txt
R$ 59,90 → 5990
```

### Authorization

Use:

- Ownership validation
- Laravel Policies

Never rely on ID obscurity for security.

### Relationships

Follow:

```txt
docs/database-design.md
docs/database-decisions.md
```

---

## Authentication

Supported authentication:

- Email / Password
- Google OAuth
- GitHub OAuth

Use:

- Sanctum
- Socialite

---

## Authorization

Use:

- Spatie Permission
- Laravel Policies

Roles:

```txt
buyer
seller
admin
```

---

## Testing

Testing framework:

- Pest

New business rules should include tests whenever possible.

---

## Coding Style

Prefer:

- Explicit naming
- Typed properties
- Typed return values
- Small methods
- Readable code

Avoid:

- Fat controllers
- Duplicated business logic
- Hidden side effects

---

## Documentation Rules

Before changing architecture, consult:

```txt
docs/requirements.md
docs/database-design.md
docs/database-decisions.md
```

Documentation must stay updated when architecture changes.

---

## Development Principles

Prefer:

- Maintainability
- Security
- Scalability
- Clear abstractions

Do not optimize prematurely.

Follow MVP scope before implementing advanced features.

---

## AI Contribution Rules

When generating code:

- Follow existing project architecture.
- Prefer consistency over creativity.
- Do not introduce new dependencies without justification.
- Respect Laravel conventions.
- Explain major architectural decisions before implementing them.
- Avoid unnecessary abstractions.