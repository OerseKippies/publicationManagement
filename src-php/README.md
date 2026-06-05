# src-php

pubM MVP Phase 2 runtime — PHP 8.3, MariaDB 10.6, VERSIO_HOSTED.

No Composer — custom `PubM\` autoloader, PDO only.

## Structure

```text
src-php/
├── Application.php          # Router + DI
├── bootstrap.php            # HTTP bootstrap
├── Autoloader.php
├── Audit/                   # Publication audit logger + repository
├── Domain/Service/          # Draft, Publication, Schedule, History, Audit services
├── Http/                    # Request, Response, Router
├── Infrastructure/          # Database, Config, Clock, Uuid, CommL gateway
└── Repository/              # PDO repositories per table
```

## Entry Points

| Path | Purpose |
|---|---|
| `public/api/publications/index.php` | MVP API |
| `public/api/health.php` | Health check |
| `scripts/migrate.php` | Apply schema |
| `scripts/process_scheduled_publications.php` | Cron scheduler |
| `scripts/verify_runtime.php` | Integration smoke test |

## Tests

```bash
php tests/run.php
```
