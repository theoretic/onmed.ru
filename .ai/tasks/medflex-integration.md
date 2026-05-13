# Task: Medflex Integration

## Status
- [x] `get_doctors.php` — fetches all doctors from Medflex API, saves to `doctors.json`
- [ ] `assign_id_medflex.php` — assign `medflex_id` to ProcessWire specialist pages (in progress)

## Goal
Sync the `medflex_id` field on ProcessWire `specialist` pages by matching names from `doctors.json` to existing pages.

## Scripts

| File | Purpose |
|------|---------|
| `webroot/tools/import/medflex/get_doctors.php` | Fetch all pages from `GET /models/doctor/` (size=50), save to `doctors.json` |
| `webroot/tools/import/medflex/assign_id_medflex.php` | Match `doctors.json` entries to PW specialist pages by name, write `medflex_id` |
| `webroot/tools/import/medflex/doctors.json` | Cached doctor data from Medflex |

## Medflex API Reference
- Docs: https://developer.medflex.ru/clinic-site/tag/models/get_doctors
- Base URL: `https://api.medflex.ru`
- Auth: `Authorization: Token b45ee8d90e994c97c2830a4b50d9ba15defd42c68285c2bcc5dd0398f057f22a`
- Pagination field: `data[].id` is the Medflex doctor ID to write to `specialist.medflex_id`

## Notes
- `assign_id_medflex.php` must bootstrap ProcessWire from the `webroot/` dir (two levels up from `tools/import/medflex/`)
- Match strategy: by full name (TBD: exact or fuzzy)
