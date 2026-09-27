# Contributing

Thanks for considering a contribution to laravel-boilerplate.

## Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
docker compose up -d          # PostgreSQL + Mailpit
php artisan migrate --seed
npm install
composer dev                  # server + queue listener + pail + vite together
```

Or run `php artisan serve` and `npm run dev` in two separate terminals instead of `composer dev`.

## Branching and commits

- Branch off `main`.
- Use [Conventional Commits](https://www.conventionalcommits.org/) (`feat:`, `fix:`, `chore:`, `docs:`, `ci:`, ...) for commit messages and PR titles.
- Keep PRs focused on one change.

## Before opening a PR

Run the same checks CI runs, in the same order it runs them:

```bash
vendor/bin/pint --test
composer analyse
npm run lint
composer test:coverage        # or: php artisan test
npm run build
```

- Coverage floor (`composer test:coverage`, currently 60%) is a floor: it may only go up. Do not lower it to make a failing suite pass, add tests instead.
- If your PHP is not formatted, run `vendor/bin/pint` before committing.
- Do not use an em dash (U+2014) or ` -- ` as a dash in any file, including commit messages and PR text. CI's `check-dashes.sh` script fails the `lint` job on either. Use a period, colon, comma, or parentheses instead.
- If you touched `e2e/`, also run the Playwright suite locally: `npm run build && npm run test:e2e`. This needs a migrated SQLite database at `database/e2e.sqlite` (see `playwright.config.js`).
- If you changed a composer or npm dependency, commit the updated `composer.lock` or `package-lock.json`.
- If you added a new public (unauthenticated) route, add a rate limiter for it and mention the limit in SECURITY.md.

## Reporting a security issue

Do not open a public issue for a vulnerability. Follow the private reporting process in [SECURITY.md](SECURITY.md) instead.
