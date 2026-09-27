import { test, expect } from '@playwright/test';

// Runs against `php artisan serve` with a migrated SQLite database (see
// playwright.config.js). The auth routes are rate limited per IP, so the
// user is registered once and reused.
test.describe.configure({ mode: 'serial' });

const email = `e2e-${Date.now()}@example.com`;
const password = 'Password123!';

function collectCspViolations(page) {
    const violations = [];
    page.on('console', (message) => {
        if (message.type() === 'error' && /Content Security Policy/i.test(message.text())) {
            violations.push(message.text());
        }
    });
    return violations;
}

test('registers, reaches the dashboard and logs out', async ({ page }) => {
    const violations = collectCspViolations(page);

    await page.goto('/register');
    await page.getByLabel('Name').fill('E2E User');
    await page.getByLabel('Email').fill(email);
    await page.getByLabel('Password', { exact: true }).fill(password);
    await page.getByLabel('Confirm Password').fill(password);
    await page.getByRole('button', { name: 'Register' }).click();

    await expect(page).toHaveURL(/\/dashboard$/);
    await expect(page.getByText("You're logged in!")).toBeVisible();

    // The desktop and mobile menus both render a Log Out control.
    await page.getByRole('button', { name: 'E2E User' }).click();
    await page.getByRole('button', { name: 'Log Out' }).first().click();
    await expect(page).toHaveURL(/\/$/);

    expect(violations).toEqual([]);
});

test('rejects a wrong password', async ({ page }) => {
    await page.goto('/login');
    await page.getByLabel('Email').fill(email);
    await page.getByLabel('Password').fill('not-the-password');
    await page.getByRole('button', { name: 'Log in' }).click();

    await expect(page.getByText('These credentials do not match our records.')).toBeVisible();
    await expect(page).toHaveURL(/\/login$/);
});

test('signs in with the registered account', async ({ page }) => {
    await page.goto('/login');
    await page.getByLabel('Email').fill(email);
    await page.getByLabel('Password').fill(password);
    await page.getByRole('button', { name: 'Log in' }).click();

    await expect(page).toHaveURL(/\/dashboard$/);
});

test('sends a guest from the dashboard to the login page', async ({ page }) => {
    await page.goto('/dashboard');
    await expect(page).toHaveURL(/\/login$/);
});

test('serves security headers and a nonce-based CSP', async ({ page }) => {
    const violations = collectCspViolations(page);
    const response = await page.goto('/login');
    const headers = response.headers();

    expect(headers['x-content-type-options']).toBe('nosniff');
    expect(headers['x-frame-options']).toBe('DENY');
    expect(headers['content-security-policy']).toMatch(/script-src[^;]*'nonce-/);
    expect(headers['content-security-policy']).not.toMatch(/script-src[^;]*'unsafe-inline'/);

    // The page must still work under that policy: Ziggy's route() and the
    // Inertia app both run.
    await expect(page.getByRole('button', { name: 'Log in' })).toBeVisible();
    expect(violations).toEqual([]);
});
