# Production image (optional). Database and Mailpit: use docker-compose.yml.
# This image runs the Laravel app only.

# PHP dependencies. Needed before the asset build: app.js imports Ziggy from
# vendor/.
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --no-interaction --prefer-dist --ignore-platform-reqs

# Frontend assets.
FROM node:24-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY vite.config.js ./
COPY resources ./resources
COPY --from=vendor /app/vendor ./vendor
RUN npm run build

FROM php:8.4-cli-alpine

# Runtime libraries stay installed; only the -dev headers are removed after
# compiling the extensions.
RUN apk add --no-cache libpq icu-libs libzip oniguruma \
    && apk add --no-cache --virtual .build-deps libpq-dev icu-dev libzip-dev oniguruma-dev \
    && docker-php-ext-install -j"$(nproc)" pdo_pgsql intl zip pcntl bcmath \
    && apk del .build-deps

WORKDIR /app

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY --from=vendor /app/vendor ./vendor
COPY . .
COPY --from=assets /app/public/build ./public/build
RUN composer dump-autoload --optimize --no-dev \
    && adduser -D -g "" appuser \
    && chown -R appuser storage bootstrap/cache

USER appuser
EXPOSE 8000
CMD ["php", "artisan", "serve", "--host=0.0.0.0"]
