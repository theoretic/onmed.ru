# ADR-001: ProcessWire as CMS/CMF

**Date**: 2025  
**Status**: Active

## Decision
Use ProcessWire 3.x as the backend framework/CMS rather than a standalone framework (Laravel, Symfony) or a headless CMS.

## Rationale
- Flexible field/template system maps directly to the medical content model (specialists, branches, offers, feedbacks)
- Built-in hook system eliminates the need for a separate event bus
- ProcessWire's API surface (`$pages`, `$fields`, `$user`) is available in templates with no boilerplate
- Smaller footprint than Symfony/Laravel for a content-heavy portal

## Consequences
- All content types are ProcessWire templates; schema changes require the PW admin UI or API
- Module ecosystem is smaller than Laravel's; custom code is preferred for business logic
- Query cache must be disabled (`$config->dbCache = false`) due to MariaDB compatibility issue
