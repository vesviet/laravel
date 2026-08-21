# Laravel E-Commerce Storefront

A production-grade server-rendered e-commerce storefront built with Laravel 13, Filament 3, and Livewire 3. Deployed on cPanel/Docker with MySQL.

---

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Framework | Laravel 13.x (PHP 8.3+) |
| Admin Panel | Filament 3 + filament-shield (RBAC) |
| Frontend | Blade + Livewire 3 + Vite |
| Auth | Custom customer guard + Filament admin guard |
| Database | MySQL 8.0 (production), SQLite (local dev only) |
| Queue | Database-backed (upgradeable to Redis) |
| Cache | Database-backed (upgradeable to Redis) |
| Shipping | Goship API (COD + shipping calculation) |
| PDF | barryvdh/laravel-dompdf |
| Testing | Pest PHP 4 |

---

## Project Structure

```
app/
  Actions/           # Transaction owners (ADR-S2) — ProcessCheckoutAction, CancelOrderAction
  Enums/             # Typed value objects — OrderStatus, CustomerTier
  Filament/          # Admin panel resources, pages, widgets
  Http/Controllers/  # Storefront + Admin controllers (thin layer only)
  Models/            # Eloquent models
  Services/          # Business logic services (called inside Actions)
    Promotions/      # Core PromotionEngine + strategies + DTOs
    CustomerTierResolver.php  # ADR-B1: tier computation service
  Observers/         # Model observers (cache invalidation)
```

## Architecture Decisions

See [ARCHITECTURE.md](ARCHITECTURE.md) for ADR details. Key decisions:

- **ADR-S2 (Transaction Boundary)**: `DB::transaction()` is only permitted inside `app/Actions/`. Services must be called from within an active transaction. Enforced by CI fitness function.
- **ADR-S3 (Structural Trust Zones)**: `.env` and `database/*.sqlite` must never be committed to git. Pre-commit patterns enforced via CI.
- **ADR-B1 (Customer Tier Ownership)**: Tier computation lives exclusively in `CustomerTierResolver`. `PromotionRule` consumes `CustomerTier` enum values — it does not compute tiers.
- **ADR-B2 (Legacy Combo Rule)**: The 5% two-item combo discount is being migrated from hardcoded logic to a `PromotionRule` DB record. See `database/seeders/PromotionSeeder.php`.

---

## Local Development Setup

### Prerequisites

- PHP 8.3+
- Composer 2.x
- Node.js 20+ (for Vite)
- MySQL 8.0 or SQLite (local only)

### Setup

```bash
# 1. Clone and install
git clone <repo-url>
cd laravel
composer install
npm install

# 2. Environment
cp .env.example .env
php artisan key:generate

# 3. Database — SQLite for local dev
# In .env: DB_CONNECTION=sqlite
php artisan migrate
php artisan db:seed

# 4. Start dev server
php artisan serve
npm run dev
```

### Admin Panel

Access at `/admin`. Create the first admin user:

```bash
php artisan make:filament-user
php artisan shield:generate --all --panel=admin
```

---

## Running Tests

```bash
# All tests
php artisan test

# Specific suite
php artisan test --filter CustomerAuthTest
php artisan test --filter ProcessCheckoutActionTest

# With coverage
php artisan test --coverage
```

---

## Key Environment Variables

See `.env.example` for the full reference. Critical production variables:

| Variable | Production Value |
|----------|-----------------|
| `APP_DEBUG` | `false` |
| `LOG_LEVEL` | `error` |
| `SESSION_ENCRYPT` | `true` |
| `GOSHIP_TOKEN` | Required for shipping rates |
| `MAIL_MAILER` | SMTP provider credentials |

---

## CI/CD

GitHub Actions workflow (`.github/workflows/ci-cd.yml`) runs:

1. **Architectural Fitness Functions** — ADR-S2 transaction boundary check, ADR-S3 secret scanner
2. **Pest Tests** — Full test suite against MySQL
3. **Docker Build** — On `main` branch only
4. **Deploy** — Self-hosted runner, Docker Compose

---

## Deployment

See [DEPLOY.md](DEPLOY.md) for Docker Compose deployment and [CPANEL_SETUP.md](CPANEL_SETUP.md) for cPanel hosting setup.

---

## Operational Runbook

See [RUNBOOK.md](RUNBOOK.md) for on-call response procedures for checkout failures, queue worker down, and Goship API unavailability.
