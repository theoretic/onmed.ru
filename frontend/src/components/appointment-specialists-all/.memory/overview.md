# appointment-specialist — project overview

## Purpose
Native Web Component (`<appointment-specialist>`) for booking medical appointments via Medflex API (proxied server-side).

## Location
`frontend/src/components/appointment-specialist/`

## Source structure
```
src/
  index.ts          — custom element registration
  component.ts      — AppointmentSpecialist HTMLElement class
  state.ts          — Store (simple pub/sub state container)
  types.ts          — TypeScript interfaces (AppState, Schedule, etc.); re-exports AgeLimit from shared lib
  styles.css        — light-DOM styles, scoped to appointment-specialist element (no form styles — those live in appointment-form)
  api/
    client.ts       — fetch wrapper, ApiError class
    schedule.ts     — fetchSchedule()
    doctor.ts       — fetchDoctor() — returns id, name, specialityIds[]
    speciality.ts   — fetchSpecialities() — returns Speciality[] from /speciality/
    normalize.ts    — Medflex raw API → domain Schedule
  lib/
    date.ts         — date helpers (monthGrid, buildSlots, isoDate, etc.)
    dom.ts          — re-exports from frontend/src/js/lib/dom.ts
  views/
    header.ts       — widget header
    services.ts     — service list
    calendar.ts     — month calendar grid
    daySlots.ts     — time slot grid
```

## Shared lib
`frontend/src/js/lib/` — raw TS, no package. Imported by relative path `../../../../js/lib/dom`.
- `dom.ts` — `h()`, `mount()`, `clear()`, `Attrs`
- `types.ts` — `AgeLimit`

## Build
- Tool: **Vite 6** IIFE format
- Minifier: **@rollup/plugin-terser** (3 passes, toplevel mangle)
- `node_modules` live in **component dir** (own `package.json`)
- Build cmd: `node "...appointment-specialist/node_modules/vite/bin/vite.js" build`

## Output
| File | Destination |
|---|---|
| `dist/appointment-specialist.js` | `webroot/site/assets/js/components/appointment-specialist.js` |
| `dist/appointment-specialist.css` | `webroot/site/assets/css/appointment-specialist/appointment-specialist.css` |

## Runtime usage (PHP template)
```php
$js[] = '/site/assets/js/components/appointment-form.js';
$js[] = '/site/assets/js/components/appointment-specialist.js';
```
```html
<appointment-specialist></appointment-specialist>
```
`appointment-form.js` must be loaded before or alongside `appointment-specialist.js`.
No attributes. `apiBase` derived at runtime: `window.location.origin + "/api/medflex"`.

## API
- `apiBase` = `window.location.origin + "/api/medflex"` (runtime)
- Fetch order: `fetchDoctor` → parallel `fetchSpecialities?doctor_id=X` + `fetchSchedule?doctor_id=X`
- Services filtered to `doctor.specialityIds` (strict)
- `partialWarning` → amber `div.as-warning` banner (HTTP 206)
- Form rendered as `<appointment-form doctor-id=... service-id=... price=... start-time=... duration-min=... [age-min=... age-max=...]>`

## Styles
- Scoped under `appointment-specialist { ... }`
- Form styles removed — live in `appointment-form/src/styles.css`
- `--as-c-*` CSS vars declared on root element
