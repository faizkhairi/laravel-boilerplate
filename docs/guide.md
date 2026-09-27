# Development Guide

## API (Sanctum)

The boilerplate includes Laravel Sanctum. Authenticated API routes live in `routes/api.php`.

- **GET /api/user**: returns the authenticated user (requires `auth:sanctum`, throttled to 60 requests per minute via the `api` limiter).
- For SPA auth, use the same session and cookie as the web app.
- For mobile or third-party clients, use token-based auth (Sanctum tokens).

## UI Components (Shadcn-style)

Reusable Vue components are in `resources/js/Components/ui/`:

- **Button**: `variant`: default, secondary, outline, ghost; `size`: default, sm, lg, icon.
- **Card**, **CardHeader**, **CardContent**: card layout with dark mode support.

These are built with `class-variance-authority`, `clsx`, and `tailwind-merge` (no separate headless-UI library). Use the `cn()` helper from `@/lib/utils` for class merging in your own components.

## Dark Mode

Dark mode is toggled via the layout; the preference is stored in `localStorage` under `theme`. The app uses Tailwind `dark:` variants.

## Logging

Logs go to `storage/logs/laravel.log`. For daily rotation and retention, set `LOG_STACK=daily` and `LOG_DAILY_DAYS=14` in production.
