# OnMed — Architecture

## Directory Map

```
application/
├── .ai/                        # ← AI documentation (this directory)
│   ├── context.md              #   project overview & conventions
│   ├── architecture.md         #   this file
│   ├── decisions/              #   architectural decision records (ADRs)
│   ├── tasks/                  #   in-progress task notes
│   ├── prompts/                #   reusable prompt templates
│   └── memory/                 #   AI working memory (gitignored)
├── frontend/                   # Build toolchain (Gulp)
│   ├── src/                    #   source JS/Svelte/CSS/SVG
│   └── config.json             #   paths config for gulp
└── webroot/                    # Web root (ProcessWire)
    ├── index.php               #   PW bootstrap
    ├── api/                    #   REST API endpoints
    │   ├── index.php           #   dynamic controller router
    │   ├── appointment.php     #   book appointment
    │   ├── feedback.php        #   submit review
    │   ├── callback.php        #   contact callback
    │   ├── search.php          #   site search
    │   ├── validator.php       #   form validation
    │   ├── medflex/            #   Medflex proxy endpoints
    │   ├── css/                #   dynamic CSS endpoint
    │   └── img/                #   dynamic image endpoint
    ├── site/
    │   ├── config.php          #   PW config + DB credentials
    │   ├── templates/          #   one .php per content type
    │   │   └── _shared/        #   partials, email templates, layout pieces
    │   ├── shared/             #   shared backend code
    │   │   ├── autoload.php    #   registers _autoload/ dirs
    │   │   ├── _autoload/      #   auto-included on every request
    │   │   │   ├── data/       #   static data arrays
    │   │   │   ├── functions/  #   global functions
    │   │   │   └── hooks/      #   ProcessWire hook registrations
    │   ├── classes/        #   utility classes (Request, Validator, ApiLogger, Robokassa…)
    │   │   ├── models/         #   form validation schemas
    │   │   ├── hooks/          #   individual hook handler files
    │   │   └── functions/      #   reusable functions
    │   ├── modules/            #   ProcessWire modules (custom + 3rd-party)
    │   └── assets/             #   compiled frontend output (gitignored except fonts)
    └── tools/
        └── import/medflex/     #   CLI import scripts for Medflex data
```

## Content Types (ProcessWire Templates)

| Template | Description |
|----------|-------------|
| `specialist` | Doctor/specialist profile. Key fields: `medflex_id`, `rating`, `archimedURL` |
| `appointment-specialist` | Appointment booking page |
| `offer` / `offers` | Service offerings |
| `feedback` | Patient review (child page under specialist) |
| `branch` | Clinic location |
| `news` | News article |
| `program` / `programs` | Treatment programs |
| `discount` / `discounts` | Discount promotions |
| `media` / `medias` | Media gallery |
| `prices` / `offer-prices` | Pricing pages |
| `sitemap` / `sitemap-xml` | SEO sitemaps |
| `admin` | Admin dashboard |

## API Request Flow

```
Browser → POST /api/appointment.php
              ↓
         api/index.php (router: rtrim trailing slash, include matching controller)
              ↓
         ApiLogger::log(endpoint, input)  ← logged in validator.php + all medflex endpoints
              ↓
         Request::parse()  →  model schema (shared/models/)
              ↓
         Validator::validate()
              ↓
         ProcessWire page create + WireMailSmtp send email
              ↓
         JSON response { success, csrf_token }
```

## Medflex Proxy Endpoints (`webroot/api/medflex/`)

| File | Route | Purpose |
|------|-------|---------|
| `speciality.php` | `/api/medflex/speciality/` | List specialities |
| `doctor.php` | `/api/medflex/doctor/` | Doctor detail / all doctors |
| `schedule.php` | `/api/medflex/schedule/` | Available appointment slots |
| `appointment/make.php` | `/api/medflex/appointment/make/` | Create booking → Medflex `POST /direct_appointment/doctor/execute/`; returns `claim_id` embedded in success HTML |
| `appointment/cancel.php` | `/api/medflex/appointment/cancel/` | Cancel booking → Medflex `POST /direct_appointment/doctor/cancel/`; success = HTTP 204 (empty body) |

All endpoints share `_include/medflex.php` (cURL wrapper: `CONNECTTIMEOUT=5`, `TIMEOUT=15`) and call `ApiLogger::log()` (`site/shared/classes/ApiLogger.php`, autoloaded). All endpoints are in `namespace ProcessWire` and import `ApiLogger` with `use ApiLogger;`. **In production, medflex logging is disabled** via `ApiLogger::DISABLED_PREFIXES = ['medflex/']` — the `log()` calls are still in place (zero overhead, early `return`) and can be re-enabled for debugging by clearing that constant. The validator log (`validator/...`) is still active.

`appointment/make.php` specifics:
- `set_time_limit(0)` + `ignore_user_abort(true)` to survive long cURL on shared hosting
- Multiple `ApiLogger::log` checkpoints (received → model loaded → validation done → API called → result) — disabled in production via `DISABLED_PREFIXES`, but the call sites stay for fast re-enable on iOS regression
- POST payloads passed to logger as `$input->post->getArray()` (NOT `(array)$input->post`, which exposes WireInputData object internals)
- Server-side phone normalization: strips non-digits; 11-digit starting with `8` → replace with `7`
- Returns `{ success: "Большое спасибо…<span class='as-claim-id' data-id='UUID' hidden></span>" }` on `claim_id` present; `{ error }` otherwise
- try/catch `\Throwable` around `apiPost` call; exception logged and surfaced as `{ error }`

`appointment/cancel.php` specifics:
- Reads `claim_id` from form-encoded POST body (`$input->post->claim_id`)
- Success detection via `$apiMeta['http_code']` 200–299 check (not null-check — Medflex returns HTTP 204 No Content, empty body)

## Medflex Data Sync Flow

```
CLI: php tools/import/medflex/get_doctors.php
      → fetches all pages from GET /models/doctor/
      → saves doctors.json

CLI: php tools/import/medflex/assign_id_medflex.php
      → reads doctors.json
      → matches by name to ProcessWire specialist pages
      → writes medflex_id field to matched pages
```

## ProcessWire Hook System

Hooks in `webroot/site/shared/hooks/` handle:
- Auto-generating page UIDs on save (`page.save.uid.php`)
- Recalculating specialist ratings when feedbacks change (`specialist.rating.php`)
- Field manipulation helpers (matrix fields, swatches, star ratings)

Hooks are auto-loaded via `_autoload/hooks/` on every request.

## Frontend Component Model

Native Web Component bundles in `frontend/src/components/` are compiled to standalone IIFE JS bundles and dropped into `webroot/site/assets/js/components/`. They are embedded server-side via `<script src="...">` tags in templates.

### Components

| Component | Element | Purpose |
|---|---|---|
| `appointment-specialist/` | `<appointment-specialist>` | Single-doctor booking widget — fetches schedule, renders service + slot picker, passes attrs to `<appointment-form>`. Passes `doctor-name` (from `sched.doctor.name`) and `doctor-speciality` (from the selected service name). |
| `appointment-specialists-all/` | `<appointment-specialists-all>` | Multi-doctor booking widget with **custom combobox** speciality picker + native `<select>` doctor picker. Resolves `doctor-name` from `s.doctors` array by `selectedDoctorId`; resolves `doctor-speciality` from `s.specialities` by `selectedSpecialityId`. Passes both to `<appointment-form>`. |
| `appointment-form/` | `<appointment-form>` | Patient booking form — standalone, driven entirely by HTML attributes. **Observed attributes**: `doctor-id`, `doctor-name`, `doctor-speciality`, `service-id`, `price`, `start-time`, `duration-min`, `age-min`, `age-max`. On successful submit FormHelper adds `success` class to `.message` div; a `MutationObserver` watches for this, hides `.as-form-body`, reveals the **coupon card** (`.as-coupon`), and mounts a **cancel button** in `.as-cancel-wrapper`. The coupon shows Врач / Специализация (omitted if blank) / Дата / Время rows plus a print button. **Cancel flow**: `mountCancelButton(claimId, wrapper, couponDiv, messageDiv)` extracts `claim_id` from the `<span class="as-claim-id" data-id="...">` embedded in the success message HTML; POSTs form-encoded `claim_id` to `/api/medflex/appointment/cancel/`; on success swaps `messageDiv` to `message warning` with the cancel confirmation text, hides coupon + cancel wrapper; on error swaps `messageDiv` to `message error`, re-enables button for retry. **Print**: clicking print clones the coupon node to `<body>` as `.as-coupon-print-portal`, adds `as-printing-coupon` to `<body>`, calls `window.print()`, then removes clone + class in `afterprint`. Print isolation CSS in `webroot/site/assets/css/_core/as-coupon.xless` |

#### Custom Combobox (`appointment-specialists-all`)

The `appointment-specialists-all` component includes a custom combobox (replacing SlimSelect) for the speciality picker. Key features:

- **Element**: `<div class="as-combo" role="combobox">` containing:
  - `<input class="as-combo-input" type="text" aria-autocomplete="list">` — searchable text field
  - `<button class="as-combo-toggle">` — toggles full list (SVG chevron, matches site-wide select icon)
  - `<ul class="as-combo-list" role="listbox">` — rendered dynamically; hidden by default
  - `<select class="as-combo-native" aria-hidden>` — visually hidden, for a11y / form fallback

- **Behavior**:
  - Type to filter (case-insensitive substring match; matches highlighted in `<mark>`)
  - Arrow keys (↑/↓) move highlight; Home/End jump to bounds; Enter commits; Esc/Tab closes
  - Click toggle button → opens full list without stealing focus (prevents mobile keyboard pop)
  - Focus input + type → opens list with input value selected
  - List click/whitespace → closes list cleanly
  - `e.preventDefault()` on list click prevents wrapping `<label>` from refocusing input

- **State management**: All UI state (open/query/highlight) is local; combobox commits only on selection → Store.set() with cascade reset (selectedSpeciality, doctor, service, date, slot all cleared)

- **Styling**: Scoped CSS in component; chevron SVG (0.85rem, stroke #6b7280) matches site-wide `<select>` styling

#### Site-wide Select Styling

All `<select>` elements use a custom SVG chevron (defined in `webroot/site/assets/css/_core/select.xless`):
- `appearance: none` to hide browser arrow
- Background SVG: 12×12 chevron, right-aligned, no-repeat
- Padding-right 2.25rem to avoid overlap
- `::-ms-expand` hidden for IE/Edge

Identical styling applied in combobox toggle (SVG data-URI inline).

### Components

### Shared frontend lib

`frontend/src/_shared/` — TypeScript modules shared across components. Each component bundles its own copy — no runtime sharing.

| Path | File | Exports |
|---|---|---|
| `_shared/` | `date.ts` | `parseLocalMs()`, `buildSlots()`, `timeHM()`, `isoDate()`, `monthGrid()`, `addMonths()`, `startOfDay()`, `startOfMonth()`, `sameMonth()`, `RU_MONTHS`, `RU_WEEKDAYS_SHORT` |
| `_shared/lib/` | `dom.ts` | `h()`, `mount()`, `clear()`, `Attrs` |
| `_shared/lib/` | `types.ts` | `AgeLimit` |

Components do not import shared modules by full repo path. Instead each component has local re-export shims (e.g. `src/lib/dom.ts`, `src/lib/date.ts`) that forward to the shared location. This keeps internal imports short and makes the shared path a single change point.

## Dynamic CSS Compilation (`/api/css/`)

PHP-based on-demand LESS/CSS compiler at `webroot/api/css/`. Entry: `api/css/index.php` → `Style` class.

- Source files in `webroot/site/assets/css/` — sorted, concat-compiled, cached
- Extension `.xless` = LESS syntax (the `x` prefix is stripped before parsing); allows LESS files to coexist with plain `.css` in the same directory without a separate pipeline
- Global Less partials in `webroot/site/assets/css/_core/*.xless` (e.g. `as-coupon.xless`)
- Mixins defined in `_core/` (`.rounded()`, `.shadow()`, etc.)
- Compiler: `Wolfcast/Less.php` bundled in `api/css/_include/`
- Cache is invalidated automatically on file change; no manual step needed after deploying `.xless` files

## Static Asset Caching

HTTP cache headers are set at the Apache layer via per-directory `.htaccess` files. No PHP is involved for the static phase. Validators use `FileETag MTime Size` (stable across servers, unlike default INode).

| Asset | Location | TTL | Strategy |
|---|---|---|---|
| JS bundles | `webroot/site/assets/js/.htaccess` | `no-cache` | Always revalidate; 304 via ETag |
| Images (originals) | `webroot/site/assets/.htaccess` | 30 days | `public, max-age=2592000` — PW renames on upload so URLs are effectively unique |
| Fonts | `webroot/site/assets/.htaccess` | 1 year | `public, max-age=31536000, immutable` |
| Generated thumbs (static phase) | `webroot/api/img/.htaccess` | 7 days + revalidate | `public, max-age=604800, must-revalidate` |
| Generated thumbs (PHP first response) | `AbstractThumb::output()` | 7 days + revalidate | Matches static phase headers; supports `If-None-Match` / `If-Modified-Since` → 304 |

### Image generation pipeline (`/api/img/`)

URL pattern: `/api/img/<sourcePath>/<WxH>/<file>.<ext>` (e.g. `/api/img/site/assets/files/123/photo.jpg/300x200/photo.jpg.webp`).

`api/img/.htaccess` contains `RewriteCond %{REQUEST_FILENAME} !-s` — Apache rewrites to `index.php` only when the thumb file does not yet exist. After first generation Apache serves the file directly as static, never invoking PHP. Thumb URLs are content-addressed by dimensions; replacing a source image in-place will not regenerate existing thumbs (clear the thumb cache to force rebuild).

JSON API responses (handled in `webroot/api/index.php`) send `Cache-Control: no-store, no-cache, must-revalidate` + `Pragma: no-cache` to prevent Safari ITP from caching mutating endpoints.
