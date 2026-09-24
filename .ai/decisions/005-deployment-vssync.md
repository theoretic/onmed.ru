# ADR-005: Deployment via vssync, with hand-deployed exceptions

**Date**: 2026-09
**Status**: Active

## Decision
Production (Beget, SFTP) is updated by the vssync VS Code extension on `git push`. Files whose production copy must differ from the repo, or which are not part of the site, are excluded from vssync and deployed by hand: `webroot/site/config.php`, `webroot/tools/`, `webroot/site/modules/*` (except `AdminPageTool`), `webroot/vendor/`, `.lock` files. Gitignored build output (component JS, JS bundles, SVG sprite) is uploaded by vssync conditional rules when its sources change.

## Rationale
- 2026-09-24: a commit touching `config.php` made vssync upload the repo copy, whose `dbHost` is the local OSPanel server name; production lost its database (blank pages, API 500) until the file was fixed by hand. Environment-specific config must not travel with pushes.
- The same day the remote path was FTP-style (`/onmed.ru/public_html/`) while the protocol was SFTP; every upload failed while vssync reported the sync as done. On Beget SFTP the path is `/home/i/i92588et/onmed.ru/public_html/`.
- vssync treats per-file failures as a finished sync (the pushed range is not retried) and ignores delete errors, so after a failed sync: fix the cause, run `VSSync: Sync Now`, and delete remote files by hand if a push removed any.
- Modules developed in their own repos (StaticPages, OutputTransformer) are junctions locally; uploading them is a deliberate step followed by Modules → Refresh.

## Consequences
- Every deploy that touches `config.php`, `tools/`, modules or `vendor/` needs a manual upload; the commit message or task note should say so.
- Scripts in `tools/` meant for production (e.g. `assign_medflex_speciality_id.php`) are uploaded, run, and deleted.
