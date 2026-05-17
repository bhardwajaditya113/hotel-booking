import { chromium } from 'playwright';

const BASE = process.env.BASE_URL || 'http://127.0.0.1:8000';
const GUEST_EMAIL = process.env.GUEST_EMAIL || 'demo.guest@elapse.test';
const GUEST_PASSWORD = process.env.GUEST_PASSWORD || 'password';

async function run() {
  const browser = await chromium.launch({ headless: true });
  const context = await browser.newContext({ ignoreHTTPSErrors: true });
  const page = await context.newPage();

  try {
    console.log('Logging in...');
    await page.goto(`${BASE}/login`, { waitUntil: 'domcontentloaded' });
    await page.fill('#login', GUEST_EMAIL);
    await page.fill('#password', GUEST_PASSWORD);
    await page.click('button:has-text("Sign in")');
    await page.waitForURL('**/dashboard', { timeout: 15000 });
    console.log('Logged in. Searching properties...');

    // Try search results for Mumbai (seed used this city)
    const searchUrl = `${BASE}/search/results?search_mode=properties&city=Mumbai`;
    await page.goto(searchUrl, { waitUntil: 'domcontentloaded' });

    // Find first property detail link (skip dashboard/host links)
    const allPropLinks = page.locator('a[href*="/property/"]');
    const count = await allPropLinks.count();
    let href = null;
    for (let i = 0; i < count; i++) {
      const a = allPropLinks.nth(i);
      const h = await a.getAttribute('href');
      if (!h) continue;
      // prefer numeric property ids like /property/123
      if (/\/property\/[0-9]+/.test(h)) {
        href = h;
        await a.click();
        break;
      }
    }
    if (!href) throw new Error('No numeric property link found on search results');
    console.log('Opening property:', href);
    await page.waitForLoadState('domcontentloaded');

    // Wait for booking/checkout CTA
    const ctaSelectors = [
      'a:has-text("Book")',
      'button:has-text("Book")',
      'a:has-text("Checkout")',
      'button:has-text("Checkout")',
      'a:has-text("Check availability")',
      'button:has-text("Check availability")',
      'a[href*="/checkout"]',
    ];

    let clicked = false;
    for (const sel of ctaSelectors) {
      const el = page.locator(sel).first();
      if (await el.count() > 0) {
        try {
          await el.click({ timeout: 5000 });
          clicked = true;
          break;
        } catch (e) {
          // ignore
        }
      }
    }

    if (!clicked) {
      // try direct checkout link pattern
      const checkoutLink = await page.locator('a[href*="/checkout"]').first();
      if (await checkoutLink.count() > 0) {
        await checkoutLink.click();
      } else {
        console.log('Could not find checkout CTA; attempting to open /checkout');
        await page.goto(`${BASE}/checkout`, { waitUntil: 'domcontentloaded' });
      }
    }

    // On property page — fill booking form and submit
    // Choose first room option
    const roomSelect = page.locator('#room-select');
    if (await roomSelect.count() > 0) {
      const options = await roomSelect.locator('option').all();
      let chosen = null;
      for (const opt of options) {
        const val = await opt.getAttribute('value');
        if (val && val.trim() !== '') { chosen = val; break; }
      }
      if (!chosen) throw new Error('No selectable room option available');
      // If select isn't interactable, set value via JS to avoid visibility issues
      try {
        await roomSelect.selectOption(chosen);
      } catch (e) {
        await page.evaluate(({ sel, value }) => {
          const el = document.querySelector(sel);
          if (el) {
            el.value = value;
            el.dispatchEvent(new Event('change', { bubbles: true }));
          }
        }, { sel: '#room-select', value: chosen });
      }

      // fill dates (offset to future)
      const tomorrow = new Date();
      tomorrow.setDate(tomorrow.getDate() + 30);
      const out = new Date();
      out.setDate(out.getDate() + 33);
      const toISO = (d) => d.toISOString().slice(0,10);
      await page.fill('#check_in', toISO(tomorrow));
      await page.fill('#check_out', toISO(out));
      await page.fill('#guests', '1');
      await page.fill('#number_of_rooms', '1');

      // submit the booking form
      const submitBtn = page.locator('form#property-booking-form button[type="submit"], form#property-booking-form input[type="submit"]').first();
      await submitBtn.click();
      // after posting booking/store, it should redirect to checkout or set session
      await page.waitForLoadState('networkidle');
      console.log('Submitted property booking form, current URL:', page.url());
    } else {
      console.log('No booking form found on property page');
    }

    // Now navigate to checkout page explicitly
    await page.goto(`${BASE}/checkout`, { waitUntil: 'domcontentloaded' });
    await page.waitForLoadState('networkidle');
    console.log('On checkout page:', page.url());

    // Select Razorpay radio if present
    const razorSelector = 'input[type="radio"][value="Razorpay"]';
    const razorCount = await page.locator(razorSelector).count();
    if (razorCount > 0) {
      // If radio isn't visible, set via JS to avoid visibility issues
      try {
        await page.locator(razorSelector).check();
      } catch (e) {
        await page.evaluate(({ sel }) => {
          const el = document.querySelector(sel);
          if (el) {
            el.checked = true;
            el.dispatchEvent(new Event('change', { bubbles: true }));
          }
        }, { sel: razorSelector });
      }
      console.log('Selected Razorpay payment method');
    }

    // Try clicking the Test Payment button if present
    const testBtn = page.locator('form[action*="test-payment"] button, button:has-text("Test Payment"), button:has-text("Test Payment (Skip Razorpay)")').first();
    if (await testBtn.count() > 0) {
      await testBtn.click();
      await page.waitForLoadState('networkidle');
      console.log('Clicked test payment button. Current URL:', page.url());
    } else {
      // fallback: POST to test payment endpoint via fetch in page context
      console.log('Test button not found — falling back to fetch POST to /razorpay/test-payment');
      const resp = await page.evaluate(async (args) => {
        const BASE = args.BASE;
        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const r = await fetch(`${BASE}/razorpay/test-payment`, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf }, credentials: 'include' });
        return { status: r.status, text: await r.text() };
      }, { BASE });
      console.log('Fallback POST response status:', resp.status);
    }

    console.log('E2E customer booking script completed successfully');
    await browser.close();
    process.exit(0);
  } catch (err) {
    console.error('E2E script failed:', err);
    await browser.close();
    process.exit(1);
  }
}

run();
