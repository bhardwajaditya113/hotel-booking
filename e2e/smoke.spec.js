// @ts-check
import { test, expect } from '@playwright/test';

const guest = {
    login: 'demo.guest@elapse.test',
    password: 'password',
};

const host = {
    login: 'demo.host@elapse.test',
    password: 'password',
};

const admin = {
    login: 'admin@gmail.com',
    password: '111',
};

test.describe('Public portal', () => {
    test('homepage loads with hero and search', async ({ page }) => {
        await page.goto('/');
        await expect(page).toHaveTitle(/Elapse/i);
        await expect(page.getByRole('heading', { level: 1 })).toBeVisible();
        await expect(page.getByRole('button', { name: /search/i })).toBeVisible();
    });

    test('rooms and marketing pages return 200', async ({ page }) => {
        const paths = [
            '/rooms/',
            '/about',
            '/how-it-works',
            '/search',
            '/blog',
            '/contact',
        ];

        for (const path of paths) {
            const response = await page.goto(path);
            expect(response?.ok(), `${path} should load`).toBeTruthy();
        }
    });

    test('locale switch sets German copy on homepage', async ({ page }) => {
        await page.goto('/locale/de');
        await expect(page).toHaveURL(/\//);
        await expect(page.getByRole('navigation', { name: /primary/i })).toContainText('Startseite');
    });
});

test.describe('Guest portal', () => {
    test('guest signs in and opens dashboard and bookings', async ({ page }) => {
        await page.goto('/login');
        await page.locator('#login').fill(guest.login);
        await page.locator('#password').fill(guest.password);
        await page.getByRole('button', { name: /^sign in$/i }).click();

        await expect(page).toHaveURL(/\/dashboard/, { timeout: 15_000 });

        await page.goto('/user/booking');
        await expect(page).toHaveURL(/\/user\/booking/);
        await expect(page.locator('body')).toContainText(/booking/i);
    });

    test('guest can open wishlists and messages when authenticated', async ({ page }) => {
        await page.goto('/login');
        await page.locator('#login').fill(guest.login);
        await page.locator('#password').fill(guest.password);
        await page.getByRole('button', { name: /^sign in$/i }).click();
        await expect(page).toHaveURL(/\/dashboard/);

        for (const path of ['/wishlists', '/messages', '/wallet', '/loyalty']) {
            const response = await page.goto(path);
            expect(response?.ok(), path).toBeTruthy();
        }
    });
});

test.describe('Host portal', () => {
    test('host signs in and opens property dashboard', async ({ page }) => {
        await page.goto('/login');
        await page.locator('#login').fill(host.login);
        await page.locator('#password').fill(host.password);
        await page.getByRole('button', { name: /^sign in$/i }).click();
        await expect(page).toHaveURL(/\/dashboard/);

        const response = await page.goto('/property/dashboard');
        expect(response?.ok()).toBeTruthy();
        await expect(page.locator('body')).toContainText(/property|dashboard|host|listing/i);
    });
});

test.describe('Admin portal', () => {
    test('admin signs in and reaches dashboard', async ({ page }) => {
        await page.goto('/admin/login');
        await page.locator('#login').fill(admin.login);
        await page.locator('#password').fill(admin.password);
        await page.getByRole('button', { name: /^sign in$/i }).click();

        await expect(page).toHaveURL(/\/admin\/dashboard/, { timeout: 15_000 });
        await expect(page.locator('body')).toContainText(/dashboard|booking|admin/i);
    });

    test('admin booking list loads', async ({ page }) => {
        await page.goto('/admin/login');
        await page.locator('#login').fill(admin.login);
        await page.locator('#password').fill(admin.password);
        await page.getByRole('button', { name: /^sign in$/i }).click();
        await expect(page).toHaveURL(/\/admin\/dashboard/);

        const response = await page.goto('/booking/list');
        expect(response?.ok()).toBeTruthy();
    });
});
