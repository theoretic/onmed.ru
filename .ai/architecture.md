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
    │   │   ├── classes/        #   utility classes (Request, Validator, Robokassa…)
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
         api/index.php (router: include matching controller)
              ↓
         Request::parse()  →  model schema (shared/models/)
              ↓
         Validator::validate()
              ↓
         ProcessWire page create + WireMailSmtp send email
              ↓
         JSON response { success, csrf_token }
```

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
| `appointment-specialist/` | `<appointment-specialist>` | Doctor booking widget (fetches data, orchestrates UI) |
| `appointment-form/` | `<appointment-form>` | Patient booking form — reusable standalone |

### Shared frontend lib

`frontend/src/js/lib/` — raw TypeScript files imported by relative path from any component. No dedicated package.

| File | Exports |
|---|---|
| `dom.ts` | `h()`, `mount()`, `clear()`, `Attrs` |
| `types.ts` | `AgeLimit` |

Components that import from shared lib use relative path e.g. `../../../js/lib/dom`. Each component bundles its own copy — no runtime sharing.
