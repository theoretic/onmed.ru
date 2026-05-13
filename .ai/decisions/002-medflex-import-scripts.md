# ADR-002: Medflex Integration via Import Scripts

**Date**: 2025  
**Status**: Active

## Decision
Sync Medflex specialist data via CLI import scripts (not real-time API calls on page load).

## Rationale
- Medflex API is paginated and relatively slow; caching as JSON avoids per-request latency
- Allows mapping/cleanup step (`assign_id_medflex.php`) to reconcile names between systems
- Import scripts can be run manually or via cron, keeping the web request path fast

## Consequences
- `medflex_id` on specialist pages is the join key; must be maintained when new specialists are added
- `doctors.json` is stale between runs; schedule cron if near-real-time data is needed
- Import scripts are in `webroot/tools/import/medflex/` — they are gitignored but should be kept in sync with the API contract
