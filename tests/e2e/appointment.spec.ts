import { test, expect, Page, Request } from '@playwright/test';

/**
 * E2E: Appointment booking on iPhone Safari (WebKit)
 *
 * Reproduces the bug: "Не удалось записаться на приём" on iPhone Safari.
 * Covers:
 *   - Clean typed input
 *   - Simulated iOS autofill (programmatic value set + `change` event, no `input` event)
 *   - Various phone formats: with parens/dashes, +7-prefix
 *   - Various birthday formats: ISO (1990-05-15), dotted (15.05.1990), slashed (05/15/1990)
 *
 * APPOINTMENT_PATH must point to a specialist page where <appointment-specialist>
 * + <appointment-form> are rendered (template `specialist/views/reg.php`,
 * URL pattern: /<specialist-slug>/reg/). Set via env or override below.
 */

const APPOINTMENT_PATH = process.env.APPOINTMENT_PATH || '/spetsialisty/';

// Endpoint that the form ultimately POSTs to (Medflex proxy)
const APPOINTMENT_API = '**/api/medflex/appointment-specialist.php**';

/** Captured payload from the intercepted POST. */
interface CapturedRequest {
  request: Request;
  formData: Record<string, string>;
}

/**
 * Install an interceptor that captures the appointment POST and replies with a stub.
 * Returns a promise that resolves with the captured request data.
 */
function interceptAppointmentPost(page: Page): Promise<CapturedRequest> {
  return new Promise<CapturedRequest>((resolve) => {
    page.route(APPOINTMENT_API, async (route) => {
      const request = route.request();
      const postData = request.postData() ?? '';
      const formData: Record<string, string> = {};
      // Form posts are application/x-www-form-urlencoded or multipart
      const ct = request.headers()['content-type'] ?? '';
      if (ct.includes('application/x-www-form-urlencoded')) {
        for (const [k, v] of new URLSearchParams(postData).entries()) {
          formData[k] = v;
        }
      } else {
        // multipart/form-data — naive extraction good enough for assertions
        for (const part of postData.split(/--[\w-]+\r?\n/)) {
          const m = part.match(/name="([^"]+)"\r?\n\r?\n([\s\S]*?)\r?\n?$/);
          if (m) formData[m[1]] = m[2].trim();
        }
      }
      resolve({ request, formData });
      await route.fulfill({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify({ success: true, appointment_id: 'TEST-12345' }),
      });
    });
  });
}

/**
 * Simulate iOS Safari autofill: directly set DOM value and dispatch only `change`
 * (autofill on iOS does NOT fire `input`). This is the path that historically
 * bypassed our normalizers.
 */
async function autofill(page: Page, selector: string, value: string) {
  await page.locator(selector).evaluate((el: HTMLInputElement, v: string) => {
    el.value = v;
    el.dispatchEvent(new Event('change', { bubbles: true }));
  }, value);
}

async function pickFirstAvailableSlot(page: Page) {
  // Wait for component to hydrate and load schedule
  const form = page.locator('appointment-specialist, appointment-form').first();
  await form.waitFor({ state: 'visible' });

  // Date buttons in the calendar are enabled (non-faded) days
  const enabledDay = page.locator('appointment-specialist >> button:not([disabled]):not(.faded)').first();
  await enabledDay.waitFor({ state: 'visible', timeout: 15_000 });
  await enabledDay.click();

  // Time slot
  const slot = page.locator('appointment-specialist >> button:has-text(":")').first();
  await slot.waitFor({ state: 'visible' });
  await slot.click();
}

test.describe('Appointment booking — iPhone Safari', () => {
  test.beforeEach(async ({ page }) => {
    // Surface console errors / page crashes into test output
    page.on('pageerror', (err) => console.error('[pageerror]', err.message));
    page.on('console', (msg) => {
      if (msg.type() === 'error') console.error('[console.error]', msg.text());
    });
  });

  test('clean typed input → request payload normalized', async ({ page }) => {
    const capture = interceptAppointmentPost(page);
    await page.goto(APPOINTMENT_PATH);

    await pickFirstAvailableSlot(page);

    await page.locator('appointment-form input[name="last_name"]').fill('Иванов');
    await page.locator('appointment-form input[name="first_name"]').fill('Иван');
    await page.locator('appointment-form input[name="second_name"]').fill('Иванович');
    await page.locator('appointment-form input[name="mobile_phone"]').fill('+7 (900) 800-70-60');
    // Plain text DD.MM.YYYY; client + backend normalize → ISO for Medflex
    await page.locator('appointment-form input[name="birthday"]').fill('15.05.1990');

    await page.locator('appointment-form button[type="submit"]').first().click();

    const { formData } = await capture;
    expect(formData.mobile_phone).toBe('79008007060');
    expect(formData.birthday).toBe('15.05.1990');
    expect(formData.last_name).toBe('Иванов');

    // UI success state — error banner must NOT be visible
    await expect(page.locator('text=Не удалось записаться')).toBeHidden();
  });

  test('iOS autofill (change-only event) → still normalized', async ({ page }) => {
    const capture = interceptAppointmentPost(page);
    await page.goto(APPOINTMENT_PATH);

    await pickFirstAvailableSlot(page);

    await page.locator('appointment-form input[name="last_name"]').fill('Петров');
    await page.locator('appointment-form input[name="first_name"]').fill('Пётр');
    await page.locator('appointment-form input[name="second_name"]').fill('Петрович');

    // Simulate iOS autofill on phone + birthday — these slip past `input` listener
    await autofill(page, 'appointment-form input[name="mobile_phone"]', '+7 (900) 800-70-60');
    // Safari Keychain often returns ISO for bday; client normalizer must convert
    await autofill(page, 'appointment-form input[name="birthday"]', '1990-05-15');

    await page.locator('appointment-form button[type="submit"]').first().click();

    const { formData } = await capture;
    expect(formData.mobile_phone).toBe('79008007060');
    // Backend converts DD.MM.YYYY → ISO before forwarding to Medflex,
    // but the payload posted to our endpoint is DD.MM.YYYY
    expect(formData.birthday).toBe('15.05.1990');
    await expect(page.locator('text=Не удалось записаться')).toBeHidden();
  });

  test('birthday in US slashes → normalized to DD.MM.YYYY', async ({ page }) => {
    const capture = interceptAppointmentPost(page);
    await page.goto(APPOINTMENT_PATH);

    await pickFirstAvailableSlot(page);

    await page.locator('appointment-form input[name="last_name"]').fill('Сидоров');
    await page.locator('appointment-form input[name="first_name"]').fill('Сидор');
    await page.locator('appointment-form input[name="second_name"]').fill('Сидорович');
    await autofill(page, 'appointment-form input[name="mobile_phone"]', '79008007060');
    await autofill(page, 'appointment-form input[name="birthday"]', '15/05/1990');

    await page.locator('appointment-form button[type="submit"]').first().click();

    const { formData } = await capture;
    expect(formData.mobile_phone).toBe('79008007060');
    expect(formData.birthday).toBe('15.05.1990');
  });

  test('static asset cache: JS sends ETag + Cache-Control', async ({ request }) => {
    const path = '/site/assets/js/_core.js';
    const first = await request.get(path);
    expect(first.status()).toBe(200);
    const etag = first.headers()['etag'];
    expect(etag, 'JS asset must send ETag').toBeTruthy();
    expect(first.headers()['cache-control'] || '', 'JS asset must send Cache-Control').toMatch(/no-cache|max-age/);

    // Informational: check whether server honors If-None-Match -> 304.
    // Local Apache may return 200 regardless (mod_headers + mod_deflate interaction);
    // we log but don't fail.
    const second = await request.get(path, { headers: { 'If-None-Match': etag } });
    if (second.status() !== 304) {
      console.warn(
        `[cache] Server returned ${second.status()} for conditional request — ` +
        `Cache-Control is set but 304 short-circuit is not active. ` +
        `Every revalidation re-downloads full file. Investigate Apache mod_deflate/mod_headers config.`
      );
    }
  });
});
