import { defineConfig, devices } from '@playwright/test';

/**
 * iPhone Safari emulation via Playwright WebKit.
 *
 * Run:
 *   npm install
 *   npm run install:webkit       # one-time browser download (~80 MB)
 *   npm run test:local           # against https://onme.d
 *   npm run test:prod            # against https://onmed.ru
 *
 * Override URL/path per run:
 *   BASE_URL=https://onmed.ru APPOINTMENT_PATH=/some-doctor/reg/ npm test
 */

const BASE_URL = process.env.BASE_URL || 'https://onme.d';

export default defineConfig({
  testDir: './e2e',
  timeout: 60_000,
  expect: { timeout: 10_000 },
  retries: process.env.CI ? 2 : 0,
  reporter: [['list'], ['html', { open: 'never' }]],

  use: {
    baseURL: BASE_URL,
    // Local dev (https://onme.d) uses a self-signed cert
    ignoreHTTPSErrors: true,
    trace: 'retain-on-failure',
    screenshot: 'only-on-failure',
    video: 'retain-on-failure',
    locale: 'ru-RU',
    timezoneId: 'Europe/Moscow',
  },

  projects: [
    {
      name: 'iphone-safari',
      use: { ...devices['iPhone 14'] }, // WebKit + iPhone viewport + UA
    },
    {
      name: 'iphone-safari-se',
      use: { ...devices['iPhone SE'] }, // Smaller viewport edge cases
    },
    {
      name: 'desktop-safari',
      use: { ...devices['Desktop Safari'] }, // Baseline for comparison
    },
  ],
});
