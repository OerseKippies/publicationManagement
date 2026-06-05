# pubM Tests

PHP 8.3 required. No Composer/PHPUnit — custom test harness.

## Run

```bash
php tests/run.php
```

## Coverage

| Test | Validates |
|---|---|
| lifecycle draft to published | Full state machine + versions |
| schedule cron processing | Due schedule → PUBLISHED |
| audit trail immutability | Required audit actions present |
| persistence reload | Draft/publication reload from DB |
| Uuid validation | Infrastructure helpers |

## Schema

SQLite in-memory schema: `tests/support/sqlite_schema.sql`

Production schema: `schemas/001_publications.sql`
