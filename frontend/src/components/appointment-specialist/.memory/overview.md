# appointment-specialist — project overview

## Purpose
Native Web Component (`<appointment-specialist>`) for booking medical appointments via Medflex API (proxied server-side).

## Location
`z:\home\on.med\application\frontend\src\components\appointment-specialist\`

## Source structure
```
src/
  index.ts          — custom element registration
  component.ts      — AppointmentSpecialist HTMLElement class
  state.ts          — Store (simple pub/sub state container)
  types.ts          — TypeScript interfaces (AppState, Schedule, etc.)
  styles.css        — light-DOM styles, scoped to appointment-specialist element
    api/
    client.ts       — fetch wrapper, ApiError class
    schedule.ts     — fetchSchedule()
    doctor.ts       — fetchDoctor() — returns id, name, specialityIds[]
    speciality.ts   — fetchSpecialities() — returns Speciality[] from /speciality/
    normalize.ts    — Medflex raw API → domain Schedule
  lib/
    date.ts         — date helpers (monthGrid, buildSlots, isoDate, etc.)
    dom.ts          — h(), mount(), clear() tiny DOM helpers
  views/
    header.ts       — widget header (doctor name hidden; renders empty container)
    services.ts     — service list
    calendar.ts     — month calendar grid
    daySlots.ts     — time slot grid
    patientForm.ts  — booking form (light DOM, external validator)
```

## Build
- Tool: **Vite 6** via `npx vite build` invoked by gulp `makeViteComponents`
- Format: **IIFE** (classic script, no `type="module"` required)
- Minifier: **@rollup/plugin-terser** as Rollup output plugin (3 passes, toplevel mangle, aggressive compress)
- `node_modules` live in **frontend/** (parent), not in component dir
- Vite binary: `z:\home\on.med\application\frontend\node_modules\vite\bin\vite.js`

## Output
| File | Destination |
|---|---|
| `dist/appointment-specialist.js` | `../webroot/site/assets/js/components/appointment-specialist.js` |
| `dist/appointment-specialist.css` | `../webroot/site/assets/css/appointment-specialist/appointment-specialist.css` |

Approximate sizes (production): **~9.8 kB JS / ~5.6 kB CSS** (gzip: 4 / 1.3 kB).

## Gulp integration
`makeViteComponents` in `frontend/gulpfile.js`:
- detects Vite components by presence of `vite.config.ts` in folder
- runs `node <vite.js> build` via `child_process.exec` with `cwd` set to component dir
- copies `dist/*.js` → `assets/js/components/` and `dist/*.css` → `assets/css/{name}/`
- watch: `src/components/*/src/**/*.{ts,js,css}`

## Runtime usage (PHP template)
```php
$js[] = '/site/assets/js/components/appointment-specialist.js';
```
```html
<appointment-specialist
  api_base=<?=$apiBase?>
></appointment-specialist>
```
Attributes: `api_base` (defaults to `.env.production` value). `doctor_id` removed — widget fetches doctor ID from `/doctor/` API response (`data[0].id`).

## API
- Fetch order: `fetchDoctor` first → then `fetchSpecialities?doctor_id=X` + `fetchSchedule?doctor_id=X` in parallel
- `doctor.specialityIds` used to filter+name schedule services (strict: services not in specialityIds removed); fallback: if empty, kept as-is
- `fetchSchedule` returns `ScheduleResult { schedule, warning? }` — `warning` set when HTTP 206 (partial data)
- `partialWarning?: string` in `AppState`; rendered as `div.as-warning` amber banner at top of body
- Slot times: local `"YYYY-MM-DD HH:MM"` format (matches Medflex API output); `buildSlots` emits local strings
- Proxied server-side (no auth header in client)
- Form submits to `/api/medflex/appointment-specialist` (handled by external page-level form utility via `data-action` / `data-validator`)

## Styles (styles.css)
- `.as-item--selected`: border `var(--as-c-primary)`, background `color-mix(in srgb, var(--as-c-primary) 25%, transparent)`
- `.as-skeleton` also gets class `loading`

## Form fields (patientForm.ts)
Row 1 (flex): `last_name`, `first_name`, `second_name` — all required
Row 2 (flex): `mobile_phone` (tel), `birthday` (text, placeholder ДД.ММ.ГГГГ) — both required
- `birthday` has inline input mask: auto-inserts dots → `01011980` → `01.01.1980`
Single: `comment` textarea (optional), `consent` checkbox (required)
- Consent label text: "Отсылая эту форму, я соглашаюсь на [обработку персональных данных](/personal-data-consent)" (link opens in new tab)
Hidden: `doctor_id`, `service_id`, `start_time`
Submit button: `class="ML"`
CSS: `.as-form-row { display:flex; gap }` + `.as-form-row .as-field { flex:1 }`

## Environment
- `.env.production` — `VITE_API_BASE=https://proxy.onmed.example/medflex`
- `__API_BASE__` injected at build time; overridable at runtime via `api_base` attribute
