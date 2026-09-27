# Livewire Variant (All-PHP Stack)

This boilerplate uses Inertia + Vue 3 by default. If you prefer an all-PHP stack with Livewire and Blade, create a new Laravel app and install Breeze with the Livewire stack:

```bash
composer create-project laravel/laravel my-app
cd my-app
composer require laravel/breeze --dev
php artisan breeze:install livewire
```

That gives you the same auth flow (registration, login, email verification, password reset) with Livewire and Blade instead of Inertia and Vue. You can then copy over from this boilerplate as needed: `docker-compose.yml`, `.env.example` (PostgreSQL + Mailpit), the Stripe checkout and webhook routes, the queued welcome email, and the `docs/` structure.
