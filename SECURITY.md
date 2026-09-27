# Security Policy

## Reporting a Vulnerability

If you discover a security vulnerability in this project, please report it responsibly.

**Do NOT open a public GitHub issue for security vulnerabilities.**

Instead, email **ifaizkhairi@gmail.com** with:

1. A description of the vulnerability
2. Steps to reproduce the issue
3. Any potential impact

You will receive acknowledgment within 48 hours and a detailed response within 5 business days.

## Supported Versions

Only the latest commit on `main` is supported. There are no maintained release branches.

## Built-in Protections

This boilerplate ships with, and CI enforces:

- **Security headers and CSP** on every response (`app/Http/Middleware/SecurityHeaders.php`): `X-Content-Type-Options: nosniff`, `Referrer-Policy`, `X-Frame-Options: DENY`, `Permissions-Policy`, HSTS on HTTPS requests, and a nonce-based Content-Security-Policy (`script-src 'self'` plus the request's Vite nonce, no `unsafe-inline`). The Vite dev origin is added to the CSP only while `public/hot` exists, so it never reaches a built response. The bundled Scramble API docs UI at `/docs/api` is exempt from the strict CSP because it loads assets this app does not control.
- **Rate limiting**: the named `api` limiter allows 60 requests per minute per authenticated user or IP (`routes/api.php`). Registration, forgot-password, and reset-password are throttled to 6 per minute. Login uses Breeze's built-in limiter (5 attempts). `POST /stripe/checkout` requires authentication and is throttled to 10 per minute.
- **Stripe webhook signature verification**: `POST /stripe/webhook` rejects requests with a missing or invalid signature.
- **Audit logging**: `App\Services\AuditLogger` and listeners in `app/Listeners` record `LOGIN`, `LOGIN_FAILED` (email only, never a password), `LOGOUT`, `REGISTRATION`, `PASSWORD_RESET_REQUESTED`, `PASSWORD_RESET`, and `EMAIL_VERIFIED` events to the `audit_logs` table.
- **Secret scanning**: gitleaks runs in CI on every push and pull request.
- **Dependency auditing**: `composer audit --locked` and `npm audit --audit-level=high` run in CI; Dependabot opens weekly update pull requests for composer, npm, and GitHub Actions dependencies.

## Security Best Practices

When using this boilerplate, ensure you:

- Never commit `.env` files or `APP_KEY` to version control
- Set `APP_DEBUG=false` in production
- Use HTTPS in production with the correct `APP_URL`
- Keep dependencies updated (`composer audit`, `npm audit`, or let Dependabot open the PR)
- Validate all input using Form Requests
- Use Eloquent or the Query Builder, never raw SQL
- Add a rate limiter to any new public (unauthenticated) route
- Rotate `STRIPE_WEBHOOK_SECRET` if it is ever exposed
- Use `LOG_STACK=daily` in production for log rotation
