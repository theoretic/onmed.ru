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
- **Required PHP settings** on the booking endpoint: `set_time_limit(0)` + `ignore_user_abort(true)` (Beget shared hosting kills long cURL requests otherwise)

### Email (WireMailSmtp)
- Confirmation emails on appointment, feedback, callback form submissions

## Entry Points

| File | Role |
|------|------|
| `webroot/index.php` | ProcessWire bootstrap |
| `webroot/api/index.php` | API router |
| `webroot/site/config.php` | Site config + DB credentials |
| `webroot/site/shared/autoload.php` | Autoloader for classes/functions/hooks |
| `webroot/site/templates/_shared/__prepend.php` | Global template prepend |

## Frontend Build

- Source: `frontend/src/`
- Output: `webroot/site/assets/`
- Run: `cd frontend && gulp` (or per-task: `gulp js`, `gulp svelte`, `gulp css`)
- **Web Components** (`frontend/src/components/*/`) are built with Vite (each component has its own `package.json` + `vite.config.ts`). Run `npm run build` inside the component dir. Output goes to `dist/`, then manually copied to `webroot/site/assets/js/`.
