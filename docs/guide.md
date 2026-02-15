# Development Guide

## API (Sanctum)

The boilerplate includes Laravel Sanctum. Authenticated API routes live in `routes/api.php`.

- **GET /api/user** — Returns the authenticated user (requires `auth:sanctum`).
- For SPA auth, use the same session/cookie as the web app.
- For mobile/third-party, use token-based auth (Sanctum tokens).

## UI Components (Shadcn-style)

Reusable Vue components are in `resources/js/Components/ui/`:

- **Button** — `variant`: default, secondary, outline, ghost; `size`: default, sm, lg, icon.
- **Card**, **CardHeader**, **CardContent** — Card layout with dark mode support.

Use the `cn()` helper from `@/lib/utils` for class merging in your components.

## Dark Mode

Dark mode is toggled via the layout; preference is stored in `localStorage` under `theme`. The app uses Tailwind `dark:` variants.

## Logging

Logs go to `storage/logs/laravel.log`. For daily rotation and retention, set `LOG_STACK=daily` and `LOG_DAILY_DAYS=14` in production.
