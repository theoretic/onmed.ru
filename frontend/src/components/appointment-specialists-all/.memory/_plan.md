# Appointment widget — appointment-specialists-all

Built on native web components. No inline css, only external css from holding page will be used.

## API endpoints

All endpoints proxied server-side. `apiBase` = `window.location.origin + "/api/medflex"` (runtime).

## i18n

All UI messages hard-coded in Russian.

## UX flow

### Step 1 — Specialization select
On mount: parallel fetch `fetchAllSpecialities()` + `fetchAllDoctors()`.
Specializations select filters to those with ≥1 matching doctor (client-side).
Select wrapped in `<label class="as-select-label">Специализация</label>`, blank first option.

### Step 2 — Doctor select
Shown after specialization chosen. Doctors filtered client-side by `specialityIds.includes(selectedSpecialityId)`.
Select wrapped in `<label class="as-select-label">Врач</label>`, blank first option.
Selected doctor option shows `"Name — Price ₽, Duration"` once schedule loaded.

On doctor pick: `fetchDoctor?doctor_id=X` → parallel `fetchSpecialities?doctor_id=X` + `fetchSchedule?doctor_id=X`.
Service auto-selected: matches `selectedSpecialityId` or falls back to `services[0]`.
No service selection step (as-srv-list removed).

### Calendar
Displayed immediately after doctor picked. 1-month grid (Mon-first). Prev/next month nav.
Day states: `disabled` (no slots), `free` (available), `partial` (some booked). Past dates not clickable.

### Time slots
Clickable tags for each free interval slot. Disabled if overlaps booked interval.

### Form
`<appointment-form doctor-id=... service-id=... price=... start-time=... duration-min=... [age-min=... age-max=...]>`
Handled by `appointment-form` web component (must be loaded first).
