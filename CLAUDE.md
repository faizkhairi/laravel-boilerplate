# Laravel Boilerplate: AI Development Guide

Guidance for an AI assistant (or a new contributor) working in this repository. It needs no third-party SaaS accounts to run.

## Tech Stack

| Layer | Technology |
|-------|------------|
| Framework | Laravel 13 (PHP ^8.3; CI and the Dockerfile use 8.4) |
| Auth | Laravel Breeze (Inertia + Vue stack) |
| Frontend | Inertia.js 3, Vue 3, Vite 8, Tailwind CSS 4 (CSS-first config, no `tailwind.config.js`) |
| Database | PostgreSQL 16 (default), MySQL 8 |
| Email | Laravel Mail + SMTP (Mailpit in dev) |
| API | Laravel Sanctum; `routes/api.php`; `GET /api/user` |
| Queue | Database driver (opt-in); `SendWelcomeEmail` job, `WelcomeMail` mailable |
| Payments | Stripe (opt-in); `App\Services\StripeService`, `App\Http\Controllers\StripeController` |
| RBAC | Spatie Laravel Permission; roles `admin`, `user` seeded by `RolesAndPermissionsSeeder` |
| Audit logging | `App\Models\AuditLog`, `App\Services\AuditLogger`; auto-discovered listeners in `app/Listeners` |
| API docs | Scramble, OpenAPI docs at `/docs/api` |
| Testing | PHPUnit 12, Larastan (level 7), Pint, ESLint, Playwright |

## Commands

```bash
composer install
cp .env.example .env
php artisan key:generate
docker compose up -d          # PostgreSQL + Mailpit
php artisan migrate --seed
npm install
composer dev                  # server + queue listener + pail + vite together
```

Checks:

```bash
vendor/bin/pint --test        # PHP formatting
composer analyse              # Larastan, level 7
npm run lint                  # ESLint, resources/js, 0 warnings allowed
composer test                 # PHPUnit
composer test:coverage        # PHPUnit with a 60% coverage floor (needs pcov/Xdebug)
npm run build && npm run test:e2e   # Playwright, needs a migrated SQLite DB at database/e2e.sqlite
```

Run the same checks CI runs before pushing; CI matrices PHP 8.3/8.4 and Node 22/24. See `.github/workflows/ci.yml`.

## Directory Structure

```
app/
├── Http/Controllers/Auth/     # Breeze auth controllers
├── Http/Controllers/DocController.php
├── Http/Controllers/StripeController.php
├── Http/Controllers/StripeWebhookController.php
├── Http/Middleware/SecurityHeaders.php
├── Jobs/SendWelcomeEmail.php
├── Listeners/                 # Auth event listeners, write to audit_logs
├── Mail/WelcomeMail.php
├── Models/
├── Services/                  # AuditLogger, StripeService, StructuredLogger
bootstrap/app.php              # Middleware registration, CSRF exceptions
docs/                          # Markdown, served at /docs (auth required)
resources/
├── css/app.css                # Tailwind 4 CSS-first config
├── js/
│   ├── Components/ui/         # Button, Card, CardHeader, CardContent
│   ├── Layouts/
│   ├── Pages/
│   ├── lib/utils.js           # cn() class merge helper
routes/
├── web.php
├── auth.php                   # Breeze routes, throttled
├── api.php                    # Sanctum, throttled
database/migrations/, seeders/
tests/Feature, tests/Unit
e2e/                            # Playwright specs
```

## Conventions

### PHP

- `declare(strict_types=1);` at the top of every file in `app/` and `tests/`.
- Format with Pint before committing (`vendor/bin/pint`); CI runs `vendor/bin/pint --test`.
- Larastan level 7 (`phpstan.neon`) covers `app`, `config`, `database`, `routes`. Fix type issues rather than adding blanket ignores.
- Use Eloquent or the Query Builder; do not write raw SQL.
- New database changes go through a migration (`php artisan make:migration`), never manual schema edits.

### Frontend

- Inertia pages live in `resources/js/Pages/`. Use the Vue 3 Composition API.
- `@` resolves to `resources/js` (see `vite.config.js`).
- Tailwind 4 is configured in `resources/css/app.css` (`@import`, `@theme`, `@plugin`); there is no `tailwind.config.js` or `postcss.config.js` to edit.
- `resources/js/Components/ui/` holds a small set of Shadcn-style primitives (`Button`, `Card`, `CardHeader`, `CardContent`) built with `class-variance-authority`, `clsx`, and `tailwind-merge`. There is no headless-UI library (for example radix-vue) in this project; extend these primitives directly.
- Run `npm run build` before deploying, and `npm run lint` (zero warnings) before committing.

### Security

- `app/Http/Middleware/SecurityHeaders.php` sets a nonce-based Content-Security-Policy. New inline `<script>` tags will be blocked; use a `@vite` tag or attach the request's CSP nonce. `/docs/api` (Scramble) is the only route exempted from the strict CSP.
- CSRF is enabled for web routes; `stripe/webhook` is the only route excluded (`bootstrap/app.php`), because Stripe cannot send a CSRF token.
- Any new public (unauthenticated) route needs its own rate limit. Existing patterns: `throttle:api` (60/min) for `routes/api.php`, `throttle:6,1` for auth actions (register, forgot-password, reset-password), Breeze's built-in limiter for login, `throttle:10,1` plus `auth` for `/stripe/checkout`.
- Never commit `.env` or a real `APP_KEY`.

### RBAC

- Package: `spatie/laravel-permission`. The `User` model uses `HasRoles`.
- `RolesAndPermissionsSeeder` creates the `admin` and `user` roles and their permissions.
- Registering through the web form does not assign a role; assign one explicitly (`$user->assignRole('admin')`) where that is required.
- Check role: `$user->hasRole('admin')`. Check permission: `$user->can('edit users')`. Route middleware: `Route::middleware(['role:admin'])`. Blade: `@role('admin') ... @endrole`.

### Audit Logging

- `App\Services\AuditLogger` is called from listeners in `app/Listeners` that react to Laravel's built-in auth events.
- Events recorded to `audit_logs`: `LOGIN`, `LOGIN_FAILED` (stores the attempted email only, never a password), `LOGOUT`, `REGISTRATION`, `PASSWORD_RESET_REQUESTED`, `PASSWORD_RESET`, `EMAIL_VERIFIED`.

### Stripe (opt-in)

- `POST /stripe/checkout` (auth required, throttled 10/min): body `{"priceId": "price_..."}`, returns `{"url": "..."}`. Returns 503 when `STRIPE_SECRET_KEY` is unset, 422 when `priceId` fails validation.
- `POST /stripe/webhook`: verifies the Stripe signature and rejects the request (400) when it is missing or invalid. Excluded from CSRF verification in `bootstrap/app.php`.

### Email

- Dev: Mailpit (SMTP `1025`, UI `8025`). `MAIL_MAILER=smtp`, `MAIL_HOST=127.0.0.1`, `MAIL_PORT=1025`.
- On registration, `SendWelcomeEmailListener` dispatches the queued `SendWelcomeEmail` job. A delivery failure is logged; it never fails registration.
- Set `QUEUE_CONNECTION=database` and run `php artisan queue:work` to process queued jobs outside `composer dev`.

## What NOT to Do

- Do not commit `.env` or an `APP_KEY`.
- Do not add a third-party SaaS dependency (Sentry, PostHog, Resend, and similar) without documenting it as optional.
- Do not remove Breeze auth scaffolding without replacing it with equivalent auth.
- Do not add a new public route without a rate limiter.
- Do not add an inline `<script>` without the CSP nonce; it will be blocked by `SecurityHeaders`.

## Logging

Laravel logs to `storage/logs/laravel.log` (`config/logging.php`). `App\Services\StructuredLogger` adds `user_id`, IP, URL, and HTTP method to log context automatically. Set `LOG_STACK=daily` and `LOG_DAILY_DAYS=14` in production for rotation.
