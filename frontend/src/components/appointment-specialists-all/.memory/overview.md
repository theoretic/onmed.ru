# appointment-specialists-all — project overview

## Purpose
Native Web Component (`<appointment-specialists-all>`) for booking medical appointments via Medflex API. Two-step specialist selection: first by specialization, then by doctor.

## Location
`frontend/src/components/appointment-specialists-all/`

## Source structure
```
src/
  index.ts          — custom element registration
  component.ts      — AppointmentSpecialistsAll HTMLElement class
  state.ts          — Store (simple pub/sub state container)
  types.ts          — TypeScript interfaces (AppState, Schedule, etc.); re-exports AgeLimit from shared lib
  styles.css        — light-DOM styles, scoped to appointment-specialists-all element
  api/
    client.ts       — fetch wrapper, ApiError class
    schedule.ts     — fetchSchedule()
    doctor.ts       — fetchDoctor(), fetchAllDoctors() — sorted by name (ru locale)
    speciality.ts   — fetchSpecialities(base, doctorId) — per-doctor; fetchAllSpecialities(base) — all, sorted
    normalize.ts    — Medflex raw API → domain Schedule
  lib/
    date.ts         — date helpers (monthGrid, buildSlots, isoDate, etc.)
    dom.ts          — re-exports from frontend/src/js/lib/dom.ts
  views/
    header.ts       — widget header
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
- Build cmd: `node "...appointment-specialists-all/node_modules/vite/bin/vite.js" build`

## Output
| File | Destination |
|---|---|
| `dist/appointment-specialists-all.js` | `webroot/site/assets/js/components/appointment-specialists-all.js` |
| `dist/appointment-specialists-all.css` | `webroot/site/assets/css/appointment-specialists-all/appointment-specialists-all.css` |

## Runtime usage (PHP template)
```php
$js[] = '/site/assets/js/components/appointment-form.js';
$js[] = '/site/assets/js/components/appointment-specialists-all.js';
```
```html
<appointment-specialists-all></appointment-specialists-all>
```
`appointment-form.js` must be loaded before `appointment-specialists-all.js`.
No attributes. `apiBase` = `window.location.origin + "/api/medflex"` (runtime).

## API
- On mount: parallel `fetchAllSpecialities()` + `fetchAllDoctors()` → both cached in state
- Speciality select: filtered client-side to specialities with ≥1 matching doctor
- Doctor select: filtered client-side by `doctor.specialityIds.includes(selectedSpecialityId)`
- On doctor select: `fetchDoctor?doctor_id=X` → parallel `fetchSpecialities?doctor_id=X` + `fetchSchedule?doctor_id=X`
- Service auto-selected: matches `selectedSpecialityId` or falls back to `services[0]`
- `partialWarning` → amber `div.as-warning` banner (HTTP 206)
- Form rendered as `<appointment-form doctor-id=... service-id=... price=... start-time=... duration-min=... [age-min=... age-max=...]>`

## UX flow
1. **Step 1 — Specialization**: `<label class="as-select-label">` wrapping `<select>` with blank first option. Only specializations with available doctors shown.
2. **Step 2 — Doctor**: shown after specialization picked. `<label class="as-select-label">` wrapping `<select>`. Options show `"Name — Price ₽, Duration"` for the selected doctor (once schedule loads). Filtered by specialization.
3. **Calendar**: shown immediately after doctor picked, no intermediate service selection.
4. **Time slots** → **Form** (`<appointment-form>`).

## Styles
- Scoped under `appointment-specialists-all { ... }`
- `--as-c-*` CSS vars declared on root element
- `as-srv-list` / services step removed (replaced by specialization select in step 1)

