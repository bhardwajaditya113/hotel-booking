import { chromium } from '@playwright/test';

const BASE_URL = process.env.BASE_URL || 'https://www.infinityloop.online';
const ADMIN_LOGIN = process.env.ADMIN_LOGIN || 'admin@infinityloop.online';
const ADMIN_PASSWORD = process.env.ADMIN_PASSWORD || 'InfinityLoop@2026!';

function isSameOrigin(url, base) {
  try {
    const u = new URL(url, base);
    const b = new URL(base);
    return u.origin === b.origin;
  } catch {
    return false;
  }
}

async function main() {
  const browser = await chromium.launch({ headless: true });
  const context = await browser.newContext({ ignoreHTTPSErrors: true });
  const page = await context.newPage();

  const failures = [];

  page.on('pageerror', (err) => {
    failures.push({ type: 'pageerror', message: String(err) });
  });

  await page.goto(`${BASE_URL}/admin/login`, { waitUntil: 'domcontentloaded' });
  await page.fill('#login', ADMIN_LOGIN);
  await page.fill('#password', ADMIN_PASSWORD);
  await page.getByRole('button', { name: /sign in/i }).click();
  await page.waitForURL(/\/admin\/dashboard/, { timeout: 20000 });

  // Expand all collapsible groups in sidebar
  const toggles = page.locator('#sidebar-menu a[data-bs-toggle="collapse"]');
  const toggleCount = await toggles.count();
  for (let i = 0; i < toggleCount; i++) {
    const t = toggles.nth(i);
    try { await t.click({ timeout: 1000 }); } catch {}
    try { await t.click({ timeout: 1000 }); } catch {}
  }

  const links = await page.$$eval('#sidebar-menu a[href]', (els) =>
    els
      .map((a) => ({ href: a.getAttribute('href') || '', text: (a.textContent || '').trim() }))
      .filter((x) => x.href && !x.href.startsWith('#') && !x.href.startsWith('javascript:'))
  );

  const uniq = [];
  const seen = new Set();
  const baseOrigin = new URL(BASE_URL).origin;
  for (const l of links) {
    const abs = new URL(l.href, baseOrigin).href;
    if (!seen.has(abs)) {
      seen.add(abs);
      uniq.push({ ...l, href: abs });
    }
  }

  const results = [];

  for (const link of uniq) {
    if (!isSameOrigin(link.href, BASE_URL)) continue;

    let status = null;
    let finalUrl = link.href;
    let ok = true;
    let reason = '';

    try {
      const resp = await context.request.get(link.href, {
        timeout: 12000,
        maxRedirects: 5,
      });

      status = resp.status();
      finalUrl = resp.url();

      const bodyText = (await resp.text()).toLowerCase();
      if (
        status >= 400 ||
        finalUrl.includes('/admin/login') ||
        bodyText.includes('server error') ||
        bodyText.includes('undefined variable') ||
        bodyText.includes('view [') ||
        bodyText.includes('sqlstate[') ||
        bodyText.includes('route [')
      ) {
        ok = false;
        reason = `status=${status}`;
      }
    } catch (e) {
      ok = false;
      reason = String(e.message || e);
    }

    if (!ok) {
      failures.push({ type: 'link', text: link.text, href: link.href, finalUrl, status, reason });
    }

    results.push({ text: link.text, href: link.href, status, finalUrl, ok, reason });
  }

  await browser.close();

  console.log(JSON.stringify({ baseUrl: BASE_URL, checked: results.length, failures }, null, 2));

  if (failures.length > 0) process.exit(2);
}

main().catch((e) => {
  console.error(e);
  process.exit(1);
});
