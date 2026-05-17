import { chromium } from 'playwright';

/**
 * Audit script: Test all user dashboard sidebar menu links in authenticated session
 * Usage: node scripts/check-user-dashboard.mjs
 */

const BASE_URL = process.env.BASE_URL || 'https://www.infinityloop.online';
const GUEST_EMAIL = process.env.GUEST_EMAIL || 'demo.guest@elapse.test';
const GUEST_PASSWORD = process.env.GUEST_PASSWORD || 'password';

async function main() {
  const browser = await chromium.launch({ headless: true });
  const context = await browser.newContext({ ignoreHTTPSErrors: true });
  const page = await context.newPage();

  const failures = [];
  const passes = [];

  // Capture JavaScript errors
  page.on('pageerror', (err) => {
    failures.push({
      type: 'pageerror',
      message: String(err),
      url: page.url(),
    });
  });

  // Log in as guest
  console.log(`\n📍 Logging in as ${GUEST_EMAIL}...`);
  await page.goto(`${BASE_URL}/login`, { waitUntil: 'domcontentloaded' });
  await page.fill('#login', GUEST_EMAIL);
  await page.fill('#password', GUEST_PASSWORD);
  await page.getByRole('button', { name: /sign in/i }).click();

  // Wait for dashboard to load
  await page.waitForURL(`${BASE_URL}/dashboard`, { timeout: 20000 });
  console.log('✓ Logged in successfully');

  // Extract all links from sidebar
  console.log('\n📋 Extracting sidebar menu items...');
  const menuItems = await page.evaluate(() => {
    const items = [];
    const links = document.querySelectorAll('aside.service-side-bar a');
    links.forEach((link) => {
      const href = link.getAttribute('href');
      const text = link.textContent.trim();
      if (href && !href.includes('logout')) {
        items.push({ text, href });
      }
    });
    return items;
  });

  console.log(`Found ${menuItems.length} menu items to check:`);
  menuItems.forEach(({ text }) => console.log(`  • ${text}`));

  // Test each link
  console.log('\n🔍 Checking links...\n');
  for (const { text, href } of menuItems) {
    try {
      const response = await page.goto(href, { waitUntil: 'domcontentloaded', timeout: 15000 });
      const status = response?.status();

      // Check for JS errors on the page
      let hasError = false;
      const pageErrors = [];
      page.once('pageerror', (err) => {
        hasError = true;
        pageErrors.push(String(err));
      });

      // Check for 404/500 error patterns in content
      const body = await page.content();
      const hasErrorContent = body.includes('not found') || body.includes('error') || status >= 400;

      if (status >= 400 || hasError || (hasErrorContent && status === 200)) {
        failures.push({
          link: text,
          href,
          status,
          errors: pageErrors,
        });
        console.log(`❌ ${text}`);
        console.log(`   URL: ${href}`);
        console.log(`   Status: ${status}`);
      } else {
        passes.push({ link: text, href, status });
        console.log(`✓ ${text} (${status})`);
      }
    } catch (error) {
      failures.push({
        link: text,
        href,
        error: error.message,
      });
      console.log(`❌ ${text}`);
      console.log(`   Error: ${error.message}`);
    }
  }

  // Summary
  console.log('\n' + '='.repeat(60));
  console.log(`\n📊 Summary:`);
  console.log(`  ✓ Passed: ${passes.length}`);
  console.log(`  ❌ Failed: ${failures.length}`);
  console.log(`  Total: ${menuItems.length}`);

  if (failures.length > 0) {
    console.log('\n❌ Failed Links:');
    failures.forEach((f) => {
      console.log(`  • ${f.link || 'Unknown'}`);
      if (f.href) console.log(`    URL: ${f.href}`);
      if (f.status) console.log(`    Status: ${f.status}`);
      if (f.error) console.log(`    Error: ${f.error}`);
    });
  }

  await browser.close();

  process.exit(failures.length > 0 ? 1 : 0);
}

main().catch(console.error);
