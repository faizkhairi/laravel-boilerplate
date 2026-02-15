# Getting Started

Welcome to the Laravel Boilerplate documentation.

## Quick Start

1. **Install dependencies**
   ```bash
   composer install
   npm install --legacy-peer-deps
   ```

2. **Environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Database**
   ```bash
   docker compose up -d
   php artisan migrate
   ```

4. **Run**
   ```bash
   php artisan serve
   npm run dev
   ```

Open http://localhost:8000. Emails in development: http://localhost:8025 (Mailpit).

## Optional: Stripe

Set `STRIPE_SECRET_KEY` and `STRIPE_WEBHOOK_SECRET` in `.env`. Configure your Stripe webhook to point to `POST /stripe/webhook`.

## Optional: Queue

Set `QUEUE_CONNECTION=database` and run `php artisan queue:work`. Dispatch `SendWelcomeEmail::dispatch($user)` after registration to send a welcome email via the queue.
