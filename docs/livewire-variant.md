# Livewire Variant (All-PHP Stack)

This boilerplate uses **Inertia + Vue 3** by default. If you prefer an **all-PHP** stack with **Livewire + Blade**, you can create a new Laravel app and install Breeze with the Livewire stack:

```bash
composer create-project laravel/laravel my-app
cd my-app
composer require laravel/breeze --dev
php artisan breeze:install livewire
```

That gives you the same auth (registration, login, email verification, password reset) with Livewire and Blade instead of Inertia and Vue. You can then copy over from this boilerplate: `docker-compose.yml`, `.env.example` (PostgreSQL + Mailpit), Stripe webhook, queue, and docs structure as needed.
