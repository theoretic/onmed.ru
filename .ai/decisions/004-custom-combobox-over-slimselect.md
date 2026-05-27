# 004 — Custom Combobox Over SlimSelect

**Date**: 2026-05-27  
**Status**: Completed  
**Component**: `appointment-specialists-all`

## Context

The `appointment-specialists-all` component originally used SlimSelect, a 3rd-party jQuery-like dropdown plugin bundled globally. Issues:

1. **CSS Conflicts**: Global `input{}` resets in `_core/input.xless` clashed with SlimSelect's search field styling (margin/padding/border resets)
2. **DOM Nesting**: SlimSelect injected the dropdown into `document.body`, placing it outside the component; z-index wars with the fixed header and scoping issues
3. **UX Gap**: SlimSelect forced a select-to-searchable paradigm; no toggle button affordance, native-like picker feel missing
4. **Bundle Bloat**: SlimSelect (~30 KB JS + ~4 KB CSS) added per-page overhead site-wide (even when not used)

## Decision

**Replace SlimSelect with a custom, TypeScript Web Component combobox** for the speciality picker in `appointment-specialists-all`. Keep native `<select>` for the doctor picker (simpler, accessible, no custom code needed).

## Implementation

### New Files

- `frontend/src/components/appointment-specialists-all/src/views/specCombo.ts` — standalone combobox factory
  - No dependencies (h, mount from shared dom.ts only)
  - Owns all UI state (open, query, highlight)
  - Commits only on user selection → Store.set()
  - Full WAI-ARIA 1.2 combobox pattern (role=combobox, role=listbox, aria-expanded, aria-activedescendant, aria-autocomplete, aria-selected)

### Changes to `appointment-specialists-all`

- **component.ts**:
  - Remove `declare global { Window.SlimSelect }`
  - Drop `_specSlim` field, `_destroySpecSlim()`, `_enhanceSpecSelect()` methods
  - Add `_specCombo?: SpecCombo` field
  - New `_renderCombo(s: AppState)` method — lazily creates combobox on first render; updates available list on every state change
  - Split shell into three slots: `header` | `combo` | `body` — combo lives outside body so re-renders don't detach it (preserving focus/typing state)
  - Remove inline `<select class="as-spec-select">` from `_renderBody` Step 1; combo now handles speciality pick via `createSpecCombo({ onSelect: (id) => this._store.set({...cascade reset...}) })`

- **styles.css**:
  - Remove `.ss-content` / `.ss-search input` shim rules (SlimSelect overrides)
  - Add scoped `.as-combo*` rules:
    - `.as-combo` (wrapper, position:relative)
    - `.as-combo-input` (text field, full width, padding-right reserved for toggle)
    - `.as-combo-toggle` (button, SVG chevron background, right-aligned, rotates 180° when open)
    - `.as-combo-list` (ul, absolute, max-height, scrollable, styled option items)
    - `.as-combo-opt` (li items with highlight/selected states, `<mark>` for matches)
    - `.as-combo-native` (hidden select for a11y)

### Combobox Features

| Feature | Implementation |
|---------|---|
| Search | Case-insensitive substring filter on `.name`; highlights matches in `<mark>` |
| Keyboard | ↑/↓ (move highlight, open if closed), Home/End (jump bounds), Enter (commit), Esc/Tab (close) |
| Toggle Button | Click → opens full list; doesn't steal focus (mobile-friendly — no virtual keyboard pop) |
| Click Handling | List click → `e.preventDefault()` stops bubble to wrapping `<label>` (prevents input refocus) |
| Whitespace Click | Non-option areas → closes list cleanly |
| Input Focus | `focus` handler → opens list, selects existing value via `input.select()` |
| Native Fallback | Hidden `<select>` populated with combobox items; works without JS |
| Styling | Chevron SVG (0.85rem, #6b7280 stroke) matches site-wide `<select>` styling |

### Side-by-side: Doctor Picker

Doctor `<select>` **remains native** — simpler, no custom code, full browser a11y, inherits site-wide styling:
- `appearance: none` removes browser arrow
- Custom SVG chevron (same as combobox) applied via `background-image` + `padding-right`

## Benefits

1. **No 3rd-party deps** — combobox is ~230 LOC, builds inline with component
2. **Scoped styling** — lives inside component, no global footprint
3. **Better UX** — toggle button affordance, mobile-friendly focus handling, instant search
4. **Smaller bundle** — combobox (~3 KB gzipped) vs SlimSelect (~8 KB) + compat layer; **net gain absorbed** since SlimSelect removed from global `_core.js` (saves ~30 KB JS + ~4 KB CSS site-wide)
5. **Accessible** — full ARIA, visible focus, keyboard support, native fallback
6. **Testable** — pure functions, local state, no jQuery magic

## Tradeoffs

- **Single-select only** — combobox doesn't support multiple selections (not needed for speciality picker)
- **No browser history** — combobox state not in History API (fine for form picker; not a navigation context)
- **Manual styling** — no built-in themes; tight coupling to site's color/spacing tokens (feature, not bug — always consistent)

## File Changes Summary

| File | Change |
|---|---|
| `frontend/src/components/appointment-specialists-all/src/component.ts` | Removed SlimSelect wiring; added combobox integration |
| `frontend/src/components/appointment-specialists-all/src/views/specCombo.ts` | **New** — custom combobox factory |
| `frontend/src/components/appointment-specialists-all/src/styles.css` | Removed SlimSelect shims; added combobox rules |
| `webroot/site/assets/css/_core/select.xless` | **New** — site-wide SVG chevron for all `<select>` |

## Completed Tasks

- [x] Custom combobox implementation in `specCombo.ts` (~230 LOC)
- [x] Refactored `appointment-specialists-all` component (component.ts, styles.css)
- [x] Added site-wide SVG chevron styling (`select.xless`)
- [x] Deleted SlimSelect bundle files:
  - `frontend/src/js/_core/slimselect.min.js` (removed)
  - `webroot/site/assets/css/_core/slimselect.css` (removed)
- [x] Updated architecture docs
- [x] Bug fixes (click handler, label refocus prevention)
