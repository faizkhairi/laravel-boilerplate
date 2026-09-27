# Laravel Boilerplate

Laravel starter that needs no third-party SaaS accounts. Self-contained template for building web applications with Inertia.js, Vue 3, and Tailwind CSS.

Click "Use this template" on GitHub, or see Quick Start below, to create a new repository from this boilerplate.

## Features

- **Authentication**: Laravel Breeze (Inertia + Vue): registration, login, email verification, password reset.
- **Database**: PostgreSQL 16 (default, via `docker compose`) or MySQL; migrations and seeders.
- **Frontend**: Inertia.js 3 + Vue 3 + Vite 8 + Tailwind CSS 4 (CSS-first config in `resources/css/app.css`, no `tailwind.config.js`); dark mode toggle; a small set of Shadcn-style UI primitives (`Button`, `Card`, `CardHeader`, `CardContent`) built with `class-variance-authority`, `clsx`, and `tailwind-merge`.
- **Email**: Laravel Mail + SMTP; Mailpit for local development (http://localhost:8025). A welcome email is queued after registration; delivery failures are logged, never block registration.
- **API**: Laravel Sanctum for token/SPA auth; auto-generated OpenAPI docs via Scramble at `/docs/api`.
- **RBAC**: Spatie Laravel Permission. Roles (`admin`, `user`) and permissions are created by the seeder. Registration itself does not assign a role.
- **Audit logging**: `AuditLogger` service records `LOGIN`, `LOGIN_FAILED` (email only, never password), `LOGOUT`, `REGISTRATION`, `PASSWORD_RESET_REQUESTED`, `PASSWORD_RESET`, and `EMAIL_VERIFIED` events to the `audit_logs` table.
- **Security**: security headers and a nonce-based CSP on every response, rate limiting on auth and Stripe routes, Stripe webhook signature verification. See [Security](#security).
- **Payments (opt-in)**: Stripe checkout session creation and a signature-verified webhook endpoint; disabled unless `STRIPE_SECRET_KEY` is set.
- **Docker**: `docker-compose.yml` for PostgreSQL + Mailpit locally, and a multi-stage production `Dockerfile`.
- **Quality gates**: Pint, Larastan (level 7), PHPUnit with a coverage floor, ESLint, Playwright e2e, all enforced in CI.

## Quick Start

Create a new project from this template, either:

```bash
npx degit faizkhairi/laravel-boilerplate my-app
```

or:

```bash
gh repo create my-app --template faizkhairi/laravel-boilerplate --clone
```

Then:

```bash
cd my-app

# PHP dependencies
composer install

# Environment
cp .env.example .env
php artisan key:generate

# Database + Mailpit
docker compose up -d
php artisan migrate --seed

# Frontend dependencies
npm install

# Run everything (server, queue worker, logs, Vite) in one command
composer dev
```

`composer dev` starts the Laravel server, a queue listener, `php artisan pail`, and Vite together. To run them separately instead, use `php artisan serve` and `npm run dev` in two terminals.

Open http://localhost:8000. View dev emails at http://localhost:8025 (Mailpit). API docs are at http://localhost:8000/docs/api.

## Environment Variables

| Variable | Description | Default |
|----------|-------------|---------|
| `APP_KEY` | Application key, set by `php artisan key:generate` | (generated) |
| `DB_CONNECTION` | `pgsql` or `mysql` | `pgsql` |
| `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` | Database connection | see `.env.example` |
| `MAIL_MAILER` | `smtp` for Mailpit or a production provider | `smtp` |
| `MAIL_HOST`, `MAIL_PORT` | Mailpit locally: `127.0.0.1`, `1025` | see `.env.example` |
| `QUEUE_CONNECTION` | `sync` (default) or `database` to queue the welcome email | `sync` |
| `LOG_STACK` | `single` or `daily` for log rotation | `single` |
| `LOG_DAILY_DAYS` | Days to keep daily logs | `14` |
| `STRIPE_SECRET_KEY` | Optional. Enables `/stripe/checkout`; the route returns 503 while unset | (unset) |
| `STRIPE_WEBHOOK_SECRET` | Optional. Required for the webhook to verify Stripe's signature | (unset) |

Production: set `APP_DEBUG=false`, `APP_ENV=production`, and a real SMTP provider.

## Scripts

| Command | Description |
|---------|-------------|
| `composer dev` | Run server, queue listener, logs, and Vite together |
| `php artisan serve` | Laravel dev server only |
| `npm run dev` | Vite dev server only |
| `npm run build` | Build frontend assets for production |
| `vendor/bin/pint --test` | Check PHP formatting (`vendor/bin/pint` to fix) |
| `composer analyse` | Larastan static analysis, level 7 |
| `npm run lint` | ESLint on `resources/js` (fails on any warning) |
| `composer test` / `php artisan test` | Run the PHPUnit suite |
| `composer test:coverage` | Run tests with a 60% coverage floor (needs pcov or Xdebug) |
| `npm run test:e2e` | Playwright e2e suite |

## Testing

- **PHPUnit 12**: `composer test`. Requires the `pdo_sqlite` extension for the in-memory test database.
- **Coverage**: `composer test:coverage` enforces a 60% floor and needs pcov or Xdebug installed.
- **Larastan**: `composer analyse`, level 7 (`phpstan.neon`), covering `app`, `config`, `database`, `routes`.
- **Pint**: `vendor/bin/pint --test` checks formatting; `vendor/bin/pint` fixes it.
- **ESLint**: `npm run lint`, zero warnings allowed.
- **Playwright**: `npm run test:e2e`. Requires `npm run build` first and a migrated SQLite database at `database/e2e.sqlite` (see the comments in `playwright.config.js`).

CI (`.github/workflows/ci.yml`) runs all of the above across PHP 8.3/8.4 and Node 22/24, plus a dependency audit (`composer audit --locked`, `npm audit --audit-level=high`) and a gitleaks secret scan.

## Project Structure

```
app/
├── Http/Controllers/        # Breeze auth, Doc, Stripe controllers
├── Http/Middleware/         # SecurityHeaders and other app middleware
├── Jobs/                    # SendWelcomeEmail
├── Listeners/               # Auth event listeners writing to audit_logs
├── Mail/                    # WelcomeMail
├── Models/
├── Services/                # AuditLogger, StripeService, StructuredLogger
bootstrap/app.php            # Middleware registration, CSRF exceptions
docs/                        # Markdown docs served at /docs (auth required)
resources/
├── css/app.css              # Tailwind 4 CSS-first config
├── js/
│   ├── Components/ui/       # Shadcn-style Button, Card, CardHeader, CardContent
│   ├── Layouts/
│   ├── Pages/
│   ├── lib/utils.js         # cn() class merge helper
routes/
├── web.php
├── auth.php                 # Breeze routes, throttled
├── api.php                  # Sanctum, throttled
database/migrations/, seeders/
tests/Feature, tests/Unit
e2e/                         # Playwright specs
```

## Security

- **Headers and CSP**: `app/Http/Middleware/SecurityHeaders.php` sets `X-Content-Type-Options: nosniff`, `Referrer-Policy`, `X-Frame-Options: DENY`, `Permissions-Policy`, HSTS on HTTPS requests, and a nonce-based Content-Security-Policy (`script-src 'self'` plus the request's Vite nonce, no `unsafe-inline`). The Vite dev origin is added to the CSP only while `public/hot` exists. `img-src` allows `https://laravel.com` for the stock Welcome page images; remove it when you replace that page. The bundled Scramble API docs UI at `/docs/api` is exempt from the strict CSP because it loads third-party assets this app does not control.
- **Rate limiting**: the named `api` limiter allows 60 requests per minute per authenticated user or IP (`routes/api.php`); registration, forgot-password, and reset-password are throttled to 6 per minute; login uses Breeze's built-in limiter (5 attempts); `POST /stripe/checkout` requires authentication and is throttled to 10 per minute.
- **Stripe webhook**: `POST /stripe/webhook` verifies Stripe's signature and rejects the request when it is missing or invalid.
- **Audit logging**: see Features above. Logged via `App\Services\AuditLogger` and auto-discovered listeners in `app/Listeners`.
- **Dependency auditing and secret scanning**: `composer audit`, `npm audit`, and gitleaks all run in CI on every push and pull request.

Report vulnerabilities as described in [SECURITY.md](SECURITY.md).

## Deployment

The `Dockerfile` is a multi-stage production build:

1. A `composer:2` stage installs PHP dependencies without dev packages.
2. A `node:24-alpine` stage builds frontend assets with Vite.
3. A `php:8.4-cli-alpine` runtime stage copies in the vendor directory and built assets, then runs as a non-root user (`appuser`).

```bash
docker build -t laravel-boilerplate .
docker run -p 8000:8000 --env-file .env laravel-boilerplate
```

The container's `CMD` runs `php artisan serve --host=0.0.0.0` on port 8000, and does not include a database. Use `docker-compose.yml` for local PostgreSQL and Mailpit, or point `DB_*` at a managed database for a real deployment.

**Production checklist**: `APP_DEBUG=false`, `APP_ENV=production`, HTTPS with the correct `APP_URL`, a real database and mail provider configured, migrations run (`php artisan migrate --force`). See [docs/livewire-variant.md](docs/livewire-variant.md) for an all-PHP (Livewire + Blade) alternative to the Inertia + Vue frontend.

## Tech Stack

| Layer | Technology |
|-------|------------|
| Framework | Laravel 13 (PHP ^8.3; CI and the Dockerfile use 8.4) |
| Auth | Laravel Breeze (Inertia + Vue stack) |
| Frontend | Inertia.js 3, Vue 3, Vite 8, Tailwind CSS 4 |
| Database | PostgreSQL 16 (default), MySQL 8 |
| Email | Laravel Mail + SMTP (Mailpit in dev) |
| API | Laravel Sanctum, Scramble (OpenAPI docs) |
| RBAC | Spatie Laravel Permission |
| Payments | Stripe (opt-in) |
| Testing | PHPUnit 12, Larastan, Pint, ESLint, Playwright |

## Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md) for local setup, the checks CI runs, and commit conventions.

## License

MIT. See [LICENSE](LICENSE).

Author: Faiz Khairi ([faizkhairi.github.io](https://faizkhairi.github.io), [@faizkhairi](https://github.com/faizkhairi))
