* Medflex API proxy

Medflex API key:
b45ee8d90e994c97c2830a4b50d9ba15defd42c68285c2bcc5dd0398f057f22a

Получение 
https://developer.medflex.ru/clinic-site/tag/models/get_doctors

## Files
- `_include/medflex.php` — shared helper: `class Medflex` in `namespace ProcessWire`
  - `Medflex::corsHeaders()` — CORS; prod allows onmed.ru + www.onmed.ru only; dev allows *
  - `Medflex::cacheGet/cacheGetStale/cacheSet` — filesystem cache
  - `Medflex::fetchAllPages($url, $apiKey, &$warnings)` — paginated GET, merges data[]
  - `Medflex::apiPost($url, $apiKey, $payload)` — single POST
- `doctor.php` — proxies /models/doctor/; TTL 12h
  - No id: fetches all doctors (all pages), key: doctor_all — intended for cron
  - Id set (from ?doctor_id or $referer->page->id_medflex): key doctor_{id}
    Lookup order: doctor_{id} cache → extract from doctor_all cache → fetch all doctors
    Fetching all doctors always populates both doctor_all and doctor_{id} caches (TTL 12h each)
- `speciality.php` — proxies /models/speciality/; global list; TTL 12h; key: speciality_global; partial warnings ignored
  - Per-doctor filtered list: key speciality_doctor_{id}, TTL 12h
- `schedule.php` — proxies /schedule/; TTL 1h; key: schedule_{id}_{dateStart}_{dateEnd}; HTTP 206 + warning field on partial page fetch
- `appointment-specialist.php` — POST booking; validates input; requires specialist page + id_medflex; calls Medflex::apiPost

## Cache
- Storage: `{site}/assets/cache/medflex/*.json`
- Format: `{ "expires_at": <unix>, "data": <payload> }`
- Stale cache served on page-1 API failure (any HTTP non-200)
- Partial page failures (page 2+): data returned uncached with HTTP 206 (schedule only)

## Pagination
- `Medflex::fetchAllPages($baseUrl, $apiKey, &$warnings)` in _include/medflex.php
- Fetches page 1, reads `num_pages`, loops pages 2..N sequentially
- Merges all `data[]` arrays; strips `num_pages`, `links`, `count`, `current_page`
- Returns `['data' => $merged]`; on page N failure appends warning and stops (partial data)
- Returns null only if page 1 fails (caller falls back to stale cache)
