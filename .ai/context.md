# OnMed — Project Context

## What this project is

**OnMed** is a Russian medical portal for specialist appointment booking, clinic/doctor information, and patient reviews. It integrates with the **Medflex** external medical services API to sync specialist data.

## Tech Stack

| Layer | Technology |
|-------|-----------|
| CMS/Framework | ProcessWire 3.x (PHP) |
| Language | PHP 8+ with strict types |
| Database | MariaDB 11.8 |
| Frontend Build | Gulp 4 + Babel (ES2017) |
| UI Components | Native Web Components (TypeScript + Vite) |
| Email | WireMailSmtp module |
| Payment | Robokassa (integrated, not yet active) |
| External API | Medflex (`https://api.medflex.ru`) |

## Key Conventions

- **PHP**: `declare(strict_types=1)` on all files; PSR-4 autoloading via Composer + ProcessWire's own autoloader
- **Templates**: ProcessWire template = content type; template file = `webroot/site/templates/{name}.php`
- **API**: Thin controllers at `webroot/api/`; routed by `api/index.php`; all responses are JSON
- **Forms**: Declarative validation models in `webroot/site/shared/models/`; validated via `shared/classes/Validator.php`
- **Language**: URL-prefix based (`/ru/`, `/en/`); detected in `Referer.php`
- **Page UIDs**: Auto-generated on page save via ProcessWire hook
- **Debug mode**: Disabled in production; enabled automatically on Windows (dev)
- **No DB query cache**: `$config->dbCache = false` to prevent MariaDB freezing issues
- **Session lifetime**: 1 year

## External Integrations

### Medflex API
- Base URL: `https://api.medflex.ru`
- Auth: `Authorization: Token <token>`
- Key endpoints:
  - `GET /models/doctor/?page=N&size=50` — paginated doctors list (used by import scripts)
  - `GET /direct_appointment/doctor/schedule/` — fetch available schedule slots
  - `POST /direct_appointment/doctor/execute/` — create appointment; returns `{"claim_id": "..."}` on success, HTTP 423 `{"detail": "Слот уже занят."}` if slot taken
- Import scripts live in `webroot/tools/import/medflex/`
- Proxy endpoints live in `webroot/api/medflex/` (`appointment/make.php`, `appointment/cancel.php`, `doctor.php`, `schedule.php`, `speciality.php`)
- **Phone normalization**: server-side, `preg_replace('/\D/', '')` → 10-digit: prepend `7`; 11-digit starting with `8`: replace leading `8` with `7`
- **ApiLogger**: `webroot/site/shared/classes/ApiLogger.php` — autoloaded class (global namespace); toggles `const ENABLED` (global) and `const DISABLED_PREFIXES` (per-endpoint allowlist; defaults to `['medflex/']` so medflex traffic is silent in production); one log file per endpoint per day at `site/assets/logs/api_{endpoint_slug}.YYYY-MM-DD.log` (e.g. `api_validator_medflex_appointment_make.2026-05-20.log`). Used by all medflex proxy endpoints and `api/validator.php`. To debug medflex flow, temporarily clear `DISABLED_PREFIXES`.
- **Required PHP settings** on the booking endpoint: `set_time_limit(0)` + `ignore_user_abort(true)` (shared hosting kills long cURL requests otherwise; observed on Beget, kept on NetAngels)

### Email (WireMailSmtp)
- Confirmation emails on appointment, feedback, callback form submissions

## Entry Points

| File | Role |
|------|------|
| `webroot/index.php` | ProcessWire bootstrap |
| `webroot/api/index.php` | API router |
| `webroot/site/config.php` | Site config + DB credentials (not synced by vssync; production copy edited by hand) |
| `webroot/site/shared/autoload.php` | Autoloader for classes/functions/hooks |
| `webroot/site/templates/_shared/__prepend.php` | Global template prepend |

## Frontend Build

- Source: `frontend/src/`
- Output: `webroot/site/assets/`
- Run: `cd frontend && gulp` (or per-task: `gulp js`, `gulp svelte`, `gulp css`)
- **Web Components** (`frontend/src/components/*/`) are built with Vite (each component has its own `package.json` + `vite.config.ts`). `gulp` builds them and copies `dist/*.js` → `webroot/site/assets/js/components/`, `dist/*.css` → `webroot/site/assets/css/<component>/`. Unit tests: `npx vitest run` in the component dir (`tests/**/*.test.ts`).
- Built JS (`assets/js/`) and the SVG sprite are gitignored; they reach production through vssync conditional rules (see Deployment).

## Deployment

- **vssync** (VS Code extension; config in `.vssync/config.json`, gitignored): on `git push` it uploads the pushed files over FTP; `VSSync: Sync Now` uploads everything changed. Remote files are deleted only for files removed with `git rm`, on push.
- Host: NetAngels, server `h71.netangels.ru`, FTP account `c504101_onmed_pro` (container `c504101`). The FTP root is the account root, so the site root (`webroot/`) maps to `/www`; every conditional-rule mapping must use `/www/...` too. Production domain is **onmed.pro**; onmed.ru and www.onmed.ru 301-redirect to it.
- Before 2026-11 the site was on Beget (SFTP `i92588et.beget.tech`, root `/home/i/i92588et/onmed.ru/public_html/`). Those paths and that host are dead; don't reuse them.
- vssync creates missing remote directories, so a wrong account or path does not fail: it builds a stray tree, uploads there and marks the files as synced. After changing the config, check a known file on production (e.g. `https://onmed.pro/site/assets/js/_core.js`) rather than trusting the sync report.
- Conditional rules upload gitignored build output when its sources are pushed: `frontend/src/js` → `assets/js/`; `frontend/src/components|_shared` → `assets/js/components/` + component CSS dirs; `frontend/src/svg` → `assets/svg/`. Run `gulp` before pushing.
- **Never synced, deploy by hand**: `site/config.php` (production has its own copy; the repo copy's `dbHost` is the local OSPanel server), `tools/`, `site/modules/*` (incl. the StaticPages / OutputTransformer junctions), `vendor/` (after a Composer change upload the new package + `vendor/composer/`; don't run a full `composer install` on the server blindly, it removes undeclared packages).
- After module updates: Modules → Refresh in the production admin.
- See `decisions/005-deployment-vssync.md`.
