import { defineConfig, devices } from '@playwright/test';

const isCI = !!process.env.CI;
const port = 8123;

export default defineConfig({
    testDir: './e2e',
    fullyParallel: false,
    forbidOnly: isCI,
    retries: isCI ? 1 : 0,
    workers: 1,
    reporter: isCI ? [['list'], ['github'], ['html', { open: 'never' }]] : 'html',
    use: {
        baseURL: process.env.PLAYWRIGHT_BASE_URL || `http://127.0.0.1:${port}`,
        trace: 'on-first-retry',
    },
    projects: [{ name: 'chromium', use: { ...devices['Desktop Chrome'] } }],
    // Serves the built assets (run `npm run build` first) against a SQLite
    // database that `php artisan migrate --force` has prepared. Set
    // PLAYWRIGHT_BASE_URL to test a server you started yourself.
    // --no-reload matters: without it `artisan serve` passes only a short
    // allowlist of variables to the PHP server, so the env below would be
    // dropped and the app would fall back to the values in .env.
    webServer: process.env.PLAYWRIGHT_BASE_URL
        ? undefined
        : {
              command: `php artisan serve --no-reload --host=127.0.0.1 --port=${port}`,
              url: `http://127.0.0.1:${port}/up`,
              reuseExistingServer: !isCI,
              env: {
                  DB_CONNECTION: 'sqlite',
                  DB_DATABASE: 'database/e2e.sqlite',
                  MAIL_MAILER: 'log',
                  QUEUE_CONNECTION: 'sync',
                  SESSION_DRIVER: 'file',
                  CACHE_STORE: 'file',
              },
          },
});
