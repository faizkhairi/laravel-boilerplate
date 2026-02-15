# Laravel Boilerplate — AI Development Guide

## Project Overview

Production-ready Laravel boilerplate with **zero external account dependencies**. Inertia.js + Vue 3 + Tailwind for the frontend; Laravel Breeze for auth. No Clerk, Resend, Sentry, or PostHog.

**Philosophy:** Self-contained; run `composer install`, `npm install`, `docker compose up -d`, and start coding.

---

## Tech Stack

| Layer | Technology |
|-------|------------|
| Framework | Laravel 12 |
| Auth | Laravel Breeze (Inertia + Vue) |
| Frontend | Inertia.js, Vue 3, Vite, Tailwind CSS, Shadcn-style UI (radix-vue, CVA), dark mode |
| Database | PostgreSQL 16 (default), MySQL 8 |
| Email | Laravel Mail + SMTP (Mailpit in dev) |
| API | Laravel Sanctum; `routes/api.php`; `GET /api/user` |
| Queue | Database driver; `SendWelcomeEmail` job, `WelcomeMail` mailable |
| Payments | Stripe (opt-in); `StripeService`, `StripeController` (checkout + webhook) |
| RBAC | Spatie Laravel Permission; roles (admin, user), permissions, gates |
| Audit Logging | `AuditLog` model, `AuditLogger` service; tracks auth events |
| Structured Logging | `StructuredLogger` service; auto-context (user_id, IP, URL) |
| API Docs | Scramble; auto-generated OpenAPI 3.0 at `/docs/api` |
| Error Pages | Custom 404, 500, 403 with Tailwind styling |
| Email Templates | Responsive HTML table layouts (welcome, password-reset, verify-email) |
| Testing | PHPUnit (Breeze tests); Laravel Dusk for E2E (requires ZIP extension) |

---

## Directory Structure

```
app/
├── Http/Controllers/Auth/   # Breeze auth controllers
├── Http/Controllers/DocController.php
├── Http/Controllers/StripeWebhookController.php
├── Jobs/SendWelcomeEmail.php
├── Mail/WelcomeMail.php
├── Models/
├── Services/StripeService.php
docs/                        # Markdown docs (index, guide, livewire-variant)
resources/
├── js/
│   ├── Components/ui/       # Shadcn-style (Button, Card, CardHeader, CardContent)
│   ├── Layouts/
│   ├── Pages/Docs/
│   ├── lib/utils.js         # cn() for class merging
│   └── app.js
routes/
├── web.php
├── auth.php
├── api.php                  # Sanctum; GET /api/user
database/migrations/
tests/Feature, Unit
```

---

## Conventions

### Auth

- Breeze handles register, login, logout, email verification, password reset.
- Protected routes: use `auth` middleware; Inertia shared data exposes `user` when authenticated.
- Sanctum is installed for SPA/mobile API auth if needed.

### Database

- Default connection: PostgreSQL (`DB_CONNECTION=pgsql`). Use MySQL by changing `.env`.
- Migrations: `php artisan migrate`. Fresh: `php artisan migrate:fresh`.
- Never commit `.env`; use `.env.example` as template. Generate `APP_KEY` with `php artisan key:generate`.

### Email

- Dev: Mailpit (ports 1025 SMTP, 8025 UI). Set `MAIL_MAILER=smtp`, `MAIL_HOST=127.0.0.1`, `MAIL_PORT=1025`.
- Production: set `MAIL_*` to your SMTP provider. No Resend or other SaaS required.

### Frontend

- Inertia pages live in `resources/js/Pages/`. Use Vue 3 Composition API.
- Vite alias: `@` points to `resources/js` (see `vite.config.js`).
- Run `npm run build` before deploy; ensure it passes (path aliases must resolve).

### Security

- CSRF: enabled for web routes. Inertia sends `X-XSRF-TOKEN` / `X-CSRF-TOKEN`.
- Production: `APP_DEBUG=false`, HTTPS, throttle on auth routes (Breeze default).

### RBAC (Roles & Permissions)

- Package: Spatie Laravel Permission (`spatie/laravel-permission`)
- User model has `HasRoles` trait
- Seeder: `RolesAndPermissionsSeeder` creates `admin` and `user` roles + permissions
- Usage:
  - Check role: `$user->hasRole('admin')`
  - Check permission: `$user->can('edit users')`
  - Assign role: `$user->assignRole('admin')`
  - Middleware: `Route::middleware(['role:admin'])`
  - Blade: `@role('admin') ... @endrole`

### Audit Logging

- Model: `App\Models\AuditLog` (tracks user_id, event, ip_address, user_agent, metadata)
- Service: `App\Services\AuditLogger` with helper methods
- Usage:
  - `AuditLogger::logLogin($userId)`
  - `AuditLogger::logLogout($userId)`
  - `AuditLogger::logRegistration($userId)`
  - `AuditLogger::logPasswordReset($userId)`
  - `AuditLogger::logOAuthLogin($userId, 'google')`
  - `AuditLogger::logFailedLogin($email)`
- Events logged: LOGIN, LOGOUT, REGISTRATION, PASSWORD_RESET, EMAIL_VERIFIED, OAUTH_LOGIN, LOGIN_FAILED

### Structured Logging

- Service: `App\Services\StructuredLogger`
- Auto-enriches logs with: user_id, IP, URL, HTTP method
- Usage:
  - `StructuredLogger::info('message', ['key' => 'value'])`
  - `StructuredLogger::warning('message', $context)`
  - `StructuredLogger::error('message', $context, $exception)`
- Production: Set `LOG_STACK=daily` and `LOG_DAILY_DAYS=14` in `.env` for log rotation

### API Documentation

- Package: Scramble (`dedoc/scramble`)
- Auto-generates OpenAPI 3.0 docs from routes, controllers, form requests
- Access: `/docs/api` (requires authentication)
- Zero manual documentation needed — add PHPDoc comments and type hints for best results

### Stripe Integration

- Service: `App\Services\StripeService`
- Controller: `App\Http\Controllers\StripeController`
- Endpoints:
  - `POST /stripe/checkout` — Create checkout session (requires `priceId`)
  - `POST /stripe/webhook` — Handle Stripe events (CSRF-exempt)
- Webhook events handled:
  - `checkout.session.completed`
  - `customer.subscription.updated`
  - `customer.subscription.deleted`
  - `invoice.payment_succeeded`
  - `invoice.payment_failed`
- Setup: Set `STRIPE_SECRET_KEY` and `STRIPE_WEBHOOK_SECRET` in `.env`

### Error Pages

- Custom Blade views in `resources/views/errors/`
- Files: `404.blade.php`, `500.blade.php`, `403.blade.php`
- Tailwind styled with dark mode support
- Laravel automatically uses these instead of defaults

### Email Templates

- Responsive HTML table-based layouts for email client compatibility
- Files:
  - `resources/views/emails/welcome.blade.php`
  - `resources/views/emails/password-reset.blade.php`
  - `resources/views/emails/verify-email.blade.php`
- Variables: `$user`, `$resetUrl`, `$verificationUrl`
- Customize: Edit Blade files directly (logo, colors, footer)

---

## Development Workflow

### Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
docker compose up -d
php artisan migrate
npm install --legacy-peer-deps
npm run build
```

### Run

```bash
php artisan serve    # Laravel
npm run dev          # Vite (separate terminal)
```

### Tests

```bash
php artisan test
```

### Verification (post-clone)

1. `composer install` && `npm install --legacy-peer-deps`
2. `cp .env.example .env` && `php artisan key:generate`
3. `docker compose up -d` && `php artisan migrate`
4. `npm run build` (must succeed)
5. `php artisan test` (must pass)
6. Manual: register → check Mailpit 8025 → verify email → login → dashboard

---

## What NOT to Do

- Do not commit `.env` or `APP_KEY`.
- Do not add Clerk, Sentry, PostHog, Resend, or similar without documenting as optional.
- Do not remove Breeze auth scaffolding without replacing with equivalent auth.
- Do not use raw SQL; use Eloquent or Query Builder.
- Do not skip migrations for schema changes; use `php artisan make:migration`.

---

## Optional Add-ons (Included)

- **Stripe**: Set `STRIPE_SECRET_KEY` and `STRIPE_WEBHOOK_SECRET`; `POST /stripe/webhook` (CSRF-exempt). Use `App\Services\StripeService`.
- **Shadcn-style UI**: `resources/js/Components/ui/` (Button, Card, CardHeader, CardContent); `@/lib/utils` has `cn()`. Use `class-variance-authority`, `clsx`, `tailwind-merge`, `radix-vue`.
- **Docs**: `/docs` (auth required); markdown in `docs/*.md`; `DocController` renders via League CommonMark.
- **Queue**: Set `QUEUE_CONNECTION=database`; run `php artisan queue:work`. Dispatch `SendWelcomeEmail::dispatch($user)` after registration to send welcome email.
- **Dark mode**: Toggle in nav; preference in `localStorage` key `theme`; Tailwind `dark:` variants.
- **Livewire variant**: See `docs/livewire-variant.md` for an all-PHP (Livewire + Blade) alternative.

---

## Logging

Laravel logs to `storage/logs/laravel.log`. Configure channels in `config/logging.php`. For daily rotation and retention: set `LOG_STACK=daily` and `LOG_DAILY_DAYS=14` in production.
