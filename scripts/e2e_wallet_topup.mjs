import { chromium } from 'playwright';

const BASE = process.env.BASE_URL || 'http://127.0.0.1:8000';
const GUEST_EMAIL = process.env.GUEST_EMAIL || 'demo.guest@elapse.test';
const GUEST_PASSWORD = process.env.GUEST_PASSWORD || 'password';

function extractAmount(text) {
  const match = String(text || '').replace(/,/g, '').match(/₹\s*([0-9]+(?:\.[0-9]+)?)/);
  return match ? Number(match[1]) : null;
}

async function main() {
  const browser = await chromium.launch({ headless: true });
  const context = await browser.newContext({ ignoreHTTPSErrors: true });
  const page = await context.newPage();

  try {
    console.log('Guest login for wallet scenario...');
    await page.goto(`${BASE}/login`, { waitUntil: 'domcontentloaded' });
    await page.fill('#login', GUEST_EMAIL);
    await page.fill('#password', GUEST_PASSWORD);
    await page.getByRole('button', { name: /sign in/i }).click();
    await page.waitForURL('**/dashboard', { timeout: 15000 });

    await page.goto(`${BASE}/wallet`, { waitUntil: 'domcontentloaded' });
    const before = extractAmount(await page.locator('body').innerText());
    console.log('Wallet page opened. Balance before:', before);

    await page.goto(`${BASE}/wallet/add-money`, { waitUntil: 'domcontentloaded' });
    await page.fill('#amount', '500');

    const select = page.locator('#payment_method');
    if (await select.count()) {
      const option = await select.locator('option').first().getAttribute('value');
      if (option) {
        try {
          await select.selectOption(option);
        } catch {
          await page.evaluate(({ value }) => {
            const el = document.querySelector('#payment_method');
            if (el) {
              el.value = value;
              el.dispatchEvent(new Event('change', { bubbles: true }));
            }
          }, { value: option });
        }
      }
    }

    await page.getByRole('button', { name: /continue to pay/i }).click();
    await page.waitForLoadState('domcontentloaded');

    await page.getByRole('button', { name: /test wallet top-up/i }).click();
    await page.waitForURL('**/wallet', { timeout: 15000 });
    await page.waitForLoadState('domcontentloaded');

    const after = extractAmount(await page.locator('body').innerText());
    console.log('Wallet top-up complete. Balance after:', after);

    if (before !== null && after !== null && after <= before) {
      throw new Error(`Wallet balance did not increase (${before} -> ${after})`);
    }

    console.log('Wallet top-up UI scenario passed');
    await browser.close();
    process.exit(0);
  } catch (err) {
    console.error('Wallet top-up UI scenario failed:', err);
    await browser.close();
    process.exit(1);
  }
}

main();
