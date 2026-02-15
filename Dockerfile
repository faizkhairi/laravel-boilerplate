# Production Dockerfile (optional). Use with docker-compose or your orchestrator.
# Database and Mailpit: use docker-compose.yml. This image runs the Laravel app only.

FROM php:8.2-cli-alpine AS base

RUN apk add --no-cache \
    libpq-dev \
    icu-dev \
    libzip-dev \
    oniguruma-dev \
    && docker-php-ext-configure pdo_pgsql \
    && docker-php-ext-install -j$(nproc) pdo_pgsql intl zip pcntl bcmath \
    && apk del libpq-dev icu-dev libzip-dev oniguruma-dev

WORKDIR /app

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader

COPY . .
RUN composer dump-autoload --optimize

# Build frontend (run in CI or multi-stage with Node)
# RUN npm ci --legacy-peer-deps && npm run build

EXPOSE 8000
CMD ["php", "artisan", "serve", "--host=0.0.0.0"]
