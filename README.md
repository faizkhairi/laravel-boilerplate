# Laravel Boilerplate

Production-ready Laravel boilerplate with **zero external account dependencies**. Self-contained template for building web applications with Inertia.js, Vue 3, and Tailwind CSS.

**Use this template** — Click "Use this template" on GitHub to create a new repository from this boilerplate.

## Features

### Core Stack
- **Authentication** — Laravel Breeze (Inertia + Vue): registration, login, email verification, password reset
- **Database** — PostgreSQL (default) or MySQL; migrations and seeders
- **Frontend** — Inertia.js + Vue 3 + Vite + Tailwind CSS; **dark mode** (toggle + localStorage); **Shadcn-style UI components**
- **Email** — Laravel Mail + SMTP; Mailpit for local development (http://localhost:8025)
- **API** — Laravel Sanctum; auto-generated OpenAPI docs via Scramble at `/docs/api`
- **Docker** — PostgreSQL + Mailpit via `docker compose up -d`
- **Testing** — PHPUnit (Breeze auth tests); Laravel Dusk for E2E (requires PHP ZIP extension)
- **CI/CD** — GitHub Actions: install, build, test

### Production Features (100% Ready)
- **🔐 RBAC** — Spatie Laravel Permission: roles (admin, user), permissions, authorization gates; seeded on `php artisan db:seed`
- **📊 Audit Logging** — Track auth events (login, logout, registration, password reset); `AuditLog` model + `AuditLogger` service
- **🎨 Custom Error Pages** — Branded 404, 500, 403 pages with Tailwind styling and dark mode support
- **✉️ Professional Email Templates** — Responsive HTML table-based layouts (welcome, password-reset, verify-email); optimized for all email clients
- **📚 API Documentation** — Auto-generated OpenAPI 3.0 docs via Scramble at `/docs/api`; zero manual documentation needed
- **📝 Structured Logging** — `StructuredLogger` service with context (user ID, IP, URL); daily rotation via `LOG_STACK=daily`
- **💳 Stripe Integration (Opt-in)** — Full checkout + webhook handling; `StripeController` + `StripeService`; disabled by default
- **⚡ Queue Support** — `SendWelcomeEmail` job + `WelcomeMail` mailable; set `QUEUE_CONNECTION=database`

**Zero external dependencies** — No Clerk, Resend, Sentry, PostHog, or any SaaS. Run `composer install`, `npm install`, and `docker compose up -d` to start.

## Quick Start

### Prerequisites

- PHP 8.2+
- Composer
- Node.js 18+
- Docker (for PostgreSQL + Mailpit)

### Installation

```bash
# Create project from template
gh repo create my-app --template faizkhairi/laravel-boilerplate --private --clone
cd my-app

# Install PHP dependencies
composer install

# Copy environment and generate key
cp .env.example .env
php artisan key:generate

# Start Docker (PostgreSQL + Mailpit)
docker compose up -d

# Run migrations
php artisan migrate

# Install frontend dependencies and build
npm install --legacy-peer-deps
npm run build
```

### Development

```bash
# Terminal 1: Laravel
php artisan serve

# Terminal 2: Vite
npm run dev
```

Open http://localhost:8000. View dev emails at http://localhost:8025 (Mailpit).

## Environment Variables

| Variable | Description | Default |
|----------|-------------|---------|
| `APP_KEY` | Application key (run `php artisan key:generate` after copying .env) | — |
| `DB_CONNECTION` | `pgsql` or `mysql` | `pgsql` |
| `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` | Database connection | See .env.example |
| `MAIL_MAILER` | `smtp` for Mailpit/production | `smtp` |
| `MAIL_HOST`, `MAIL_PORT` | Mailpit: `127.0.0.1`, `1025` | — |
| `MAIL_FROM_ADDRESS` | Sender address | `noreply@example.com` |
| `QUEUE_CONNECTION` | `sync` (default) or `database` for queued jobs | `sync` |
| `LOG_STACK` | `single` or `daily` for log rotation | `single` |
| `LOG_DAILY_DAYS` | Days to keep daily logs | `14` |
| `STRIPE_SECRET_KEY` | (Optional) Stripe secret key | — |
| `STRIPE_WEBHOOK_SECRET` | (Optional) Stripe webhook signing secret | — |

Production: set `APP_DEBUG=false`, `APP_ENV=production`, and configure your SMTP provider.

## Usage Guide

### RBAC (Roles & Permissions)

```php
// Check user role
if (auth()->user()->hasRole('admin')) {
    // Admin-only code
}

// Check permission
if (auth()->user()->can('edit users')) {
    // User has permission
}

// Assign role to user
$user->assignRole('admin');

// In routes (middleware)
Route::middleware(['role:admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index']);
});

// In Blade templates
@role('admin')
    <a href="/admin">Admin Panel</a>
@endrole
```

### Audit Logging

```php
use App\Services\AuditLogger;

// Log authentication events
AuditLogger::logLogin($user->id);
AuditLogger::logLogout($user->id);
AuditLogger::logRegistration($user->id);
AuditLogger::logPasswordReset($user->id);
AuditLogger::logOAuthLogin($user->id, 'google');
AuditLogger::logFailedLogin($email);

// View audit logs
$logs = AuditLog::where('user_id', $userId)
    ->orderBy('created_at', 'desc')
    ->paginate(20);
```

### Structured Logging

```php
use App\Services\StructuredLogger;

// Info log with context
StructuredLogger::info('Order created', [
    'order_id' => $order->id,
    'amount' => $order->total,
]);

// Warning log
StructuredLogger::warning('Low stock', [
    'product_id' => $product->id,
    'quantity' => $product->stock,
]);

// Error log with exception
try {
    // ... code
} catch (\Exception $e) {
    StructuredLogger::error('Payment processing failed', [
        'order_id' => $order->id,
    ], $e);
}

// All logs automatically include: user_id, IP, URL, HTTP method
```

### Stripe Integration

**Setup:**
```bash
# In .env
STRIPE_SECRET_KEY=sk_test_...
STRIPE_WEBHOOK_SECRET=whsec_...
```

**Frontend (Vue):**
```javascript
// Create checkout session
const response = await fetch('/stripe/checkout', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ priceId: 'price_xxx' }),
});

const { url } = await response.json();
window.location.href = url; // Redirect to Stripe Checkout
```

**Backend:**
```php
// Routes already configured:
// POST /stripe/checkout - Create checkout session
// POST /stripe/webhook - Handle Stripe events

// Webhook handles:
// - checkout.session.completed
// - customer.subscription.updated
// - customer.subscription.deleted
// - invoice.payment_succeeded
// - invoice.payment_failed
```

### API Documentation

Access auto-generated OpenAPI docs at **`/docs/api`** (requires authentication).

Scramble automatically generates docs from:
- Route definitions in `routes/api.php`
- Controller method signatures and PHPDoc comments
- Form Request validation rules
- Response types and structures

### Email Templates

All email templates use responsive HTML table layouts:
- `resources/views/emails/welcome.blade.php`
- `resources/views/emails/password-reset.blade.php`
- `resources/views/emails/verify-email.blade.php`

To customize, edit the Blade files. Colors, logo, and footer are easily modified in-place.

### Error Pages

Custom error pages with Tailwind styling:
- `/resources/views/errors/404.blade.php`
- `/resources/views/errors/500.blade.php`
- `/resources/views/errors/403.blade.php`

Laravel automatically uses these instead of defaults.

### Queue Jobs

```bash
# Enable queue in .env
QUEUE_CONNECTION=database

# Run queue worker
php artisan queue:work

# Dispatch example job
use App\Jobs\SendWelcomeEmail;
SendWelcomeEmail::dispatch($user);
```

### Database Seeding

```bash
# Seed roles, permissions, and test users
php artisan db:seed

# Creates:
# - Roles: admin, user
# - Permissions: view dashboard, view users, create users, etc.
# - Admin user: admin@example.com
# - Regular user: test@example.com
```

## Scripts

| Command | Description |
|---------|-------------|
| `php artisan serve` | Start Laravel dev server |
| `npm run dev` | Start Vite dev server |
| `npm run build` | Build frontend for production |
| `php artisan test` | Run PHPUnit tests. **Local:** requires PHP `pdo_sqlite` extension for in-memory DB; otherwise run in CI or Docker. |
| `php artisan migrate` | Run migrations |

## Deployment

- **VPS**: PHP 8.2+, Nginx/Apache, PostgreSQL/MySQL. Run `composer install --no-dev`, `npm run build`, set `.env` for production, run migrations.
- **Laravel Forge / Envoyer**: Use this repo; configure env and deploy.
- **Docker**: Add a `Dockerfile` (PHP-FPM + Nginx) and run app in a container; use same `docker-compose` for DB + Mailpit or your DB service.

**Production checklist:** `APP_DEBUG=false`, `APP_ENV=production`, HTTPS, correct `APP_URL`, database and mail configured. Use throttle on auth routes (Breeze default). Consider `LOG_STACK=daily` and `LOG_DAILY_DAYS=14` for log rotation. See [docs/livewire-variant.md](docs/livewire-variant.md) for an all-PHP (Livewire + Blade) alternative.

## Tech Stack

| Layer | Technology |
|-------|------------|
| Framework | Laravel 12 |
| Auth | Laravel Breeze (Inertia + Vue) |
| Frontend | Inertia.js, Vue 3, Vite, Tailwind CSS, Shadcn-style UI (radix-vue, CVA) |
| Database | PostgreSQL 16 (default), MySQL 8 |
| Email | Laravel Mail + SMTP (Mailpit dev) |
| Queue | Database driver; WelcomeMail + SendWelcomeEmail job |
| Payments | Stripe (opt-in) |
| Testing | PHPUnit |

## License

MIT. See [LICENSE](LICENSE).

## Documentation

See [CLAUDE.md](CLAUDE.md) for AI-assisted development and conventions.

## Author

**Faiz Khairi** — [faizkhairi.github.io](https://faizkhairi.github.io) — [@faizkhairi](https://github.com/faizkhairi)
