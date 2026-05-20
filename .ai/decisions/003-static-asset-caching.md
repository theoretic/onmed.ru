# ADR-003: Static Asset Caching at Apache Layer

**Date**: 2026-05  
**Status**: Active

## Decision
Set HTTP cache headers in per-directory `.htaccess` files (Apache `mod_headers`) rather than in PHP. Use `FileETag MTime Size` for stable validators. PHP image generator sends matching headers on the first response and supports conditional requests (`304 Not Modified`).

## Rationale
- Safari iPhone aggressively heuristic-caches static responses that lack `Cache-Control`. With no headers, JS/CSS could remain stale for hours after deploys, causing user-visible bugs (e.g. an old `FormHelper.class.js` failing on appointment submit).
- Apache static serving is faster and cheaper than wrapping every asset in a PHP handler.
- The image generator (`webroot/api/img/`) already short-circuits to Apache static after the first request via `RewriteCond !-s`. Aligning the PHP first-response headers with the static-phase headers makes behavior consistent across the very first request and all subsequent ones.
- `FileETag MTime Size` avoids the default `INode` component, which breaks when content is served from multiple servers or after a file system migration.

## TTL Matrix

| Asset | TTL | Notes |
|---|---|---|
| JS bundles | `no-cache` (always revalidate) | Forces 304 round-trip; safest given no hash-based filenames |
| Image originals (`/site/assets/files/`) | 30 days | PW assigns unique paths on upload — replacement creates a new URL |
| Fonts | 1 year + `immutable` | Hashed filenames, never change in place |
| Generated thumbs (`/api/img/.../WxH/`) | 7 days + `must-revalidate` | URL is content-addressed by dimensions, not source mtime; revalidation safety net |
| JSON API responses | `no-store, no-cache, must-revalidate` + `Pragma: no-cache` | Mutating endpoints; defense against Safari ITP |

## Consequences
- After 7 days every cached thumb triggers a conditional request. If the thumb file mtime is unchanged the response is a small 304 — minor extra round-trip.
- If a source image is replaced in place, existing thumbs become stale. Resolution: clear the thumb cache directory; next request rebuilds and produces a new ETag.
- JS revalidation costs one round-trip per asset per page load. Acceptable trade-off for guaranteed freshness without hashed filenames. If round-trips become a bottleneck, re-enable the `?md5_file()` query string in `_shared/js.php` (controlled by `$settings->caching->disableJsCache`).

## Files touched
- `webroot/site/assets/js/.htaccess`
- `webroot/site/assets/.htaccess`
- `webroot/api/img/.htaccess`
- `webroot/api/img/_include/classes/AbstractThumb.php` (`output()` method)
- `webroot/api/index.php` (JSON `Cache-Control` headers)
