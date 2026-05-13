# appointment-form — overview

## Purpose
Reusable native Web Component (`<appointment-form>`) — patient booking form for Medflex appointments. Designed to be used standalone on any page or embedded by `<appointment-specialist>`.

## Location
`frontend/src/components/appointment-form/`

## Source structure
```
src/
  index.ts      — customElements.define('appointment-form', AppointmentForm)
  component.ts  — AppointmentForm HTMLElement; reads attrs; renders form; age validation
  styles.css    — scoped under appointment-form {}; --as-c-* vars redeclared
```

## Shared lib
Imports from `frontend/src/js/lib/` via relative path `../../../js/lib/`:
- `dom.ts` → `h()`
- `types.ts` → `AgeLimit`

## HTML Attributes

| Attr | Type | Notes |
|---|---|---|
| `doctor-id` | string | hidden `doctor_id` input |
| `service-id` | string | hidden `service_id` input |
| `price` | string | hidden `price` input |
| `start-time` | string | `YYYY-MM-DD HH:MM`; hidden `start_time` |
| `duration-min` | string | used to compute `end_time` |
| `age-min` | string | optional; activates age validation when both present |
| `age-max` | string | optional |

All in `observedAttributes` — `attributeChangedCallback` re-renders when attrs change after connection.

## Behavior
- `connectedCallback` → `_render()` → `replaceChildren(buildForm(this))`
- Age validation: capture-phase submit listener; blocks submit + shows `.as-age-warning` if age outside `[age-min, age-max]`; cleared on birthday `input` event
- Birthday field mask: auto-inserts dots `DDMMYYYY` → `DD.MM.YYYY`
- Phone field: strips non-digits, max 11 chars

## Form fields
Hidden: `doctor_id`, `service_id`, `price`, `start_time`, `end_time`
Row 1 (flex): `last_name`, `first_name`, `second_name` — all required
Row 2 (flex): `mobile_phone` (tel), `birthday` (text `ДД.ММ.ГГГГ`) — both required
Single: `comment` textarea (optional), `consent` checkbox (required)
Submit: `<button type="submit" class="ML float-right">Записаться</button>`

Form attrs: `data-action="/api/medflex/appointment-specialist"`, `data-method="post"`, `data-validator="/api/validator/medflex/appointment-specialist"`, `data-messaging="html"`, `novalidate`

## Build
- Vite 6, IIFE, name `AppointmentForm`, fileName `appointment-form.js`
- Build cmd: `node "...appointment-form/node_modules/vite/bin/vite.js" build`

## Output
| File | Destination |
|---|---|
| `dist/appointment-form.js` | `webroot/site/assets/js/components/appointment-form.js` |
| `dist/appointment-form.css` | `webroot/site/assets/css/appointment-form/appointment-form.css` |

## Deploy note
Must be loaded before any component that uses `<appointment-form>` (e.g. `appointment-specialist`).

## CSS
- Scoped under `appointment-form { ... }`
- `--as-c-*` vars redeclared with same defaults as `appointment-specialist`
- `display: block` on root element
- Classes: `.as-form`, `.as-form-row`, `.as-age-warning`, `.as-consent`
