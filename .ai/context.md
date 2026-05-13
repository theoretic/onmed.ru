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
| UI Components | Svelte (compiled via gulp-svelte) |
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
- Key endpoint: `GET /models/doctor/?page=N&size=50` — paginated doctors list
- Import scripts live in `webroot/tools/import/medflex/`

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
