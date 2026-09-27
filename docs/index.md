# Getting Started

Welcome to the Laravel Boilerplate documentation.

## Quick Start

1. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

2. **Environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Database**
   ```bash
   docker compose up -d
   php artisan migrate --seed
   ```

4. **Run**
   ```bash
   composer dev
   ```
   Or, in two separate terminals: `php artisan serve` and `npm run dev`.

Open http://localhost:8000. Emails in development: http://localhost:8025 (Mailpit).

## Optional: Stripe

Set `STRIPE_SECRET_KEY` and `STRIPE_WEBHOOK_SECRET` in `.env`. Configure your Stripe webhook to point to `POST /stripe/webhook`. The checkout route (`POST /stripe/checkout`) requires authentication and returns 503 while `STRIPE_SECRET_KEY` is unset.

## Optional: Queue

Set `QUEUE_CONNECTION=database` and run `php artisan queue:work`. Registration already dispatches `SendWelcomeEmail` to the queue; a delivery failure is logged and never blocks registration.
