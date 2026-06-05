# pubM Runtime Evidence — Schema Validation

Date: 2026-06-06
Module: publicationManagement (pubM)

---

## Schema Location

| Artifact | Path |
|---|---|
| MariaDB DDL | `schemas/001_publications.sql` |
| Migration runner | `scripts/migrate.php` |
| SQLite test schema | `tests/support/sqlite_schema.sql` |

---

## Tables Implemented

| Table | Purpose |
|---|---|
| publications | Lifecycle anchor |
| publication_drafts | Editable draft content |
| publication_templates | Reusable templates |
| publication_channels | Channel configuration |
| publication_targets | Publication destinations |
| publication_schedules | Cron-driven scheduling |
| publication_versions | Immutable version snapshots |
| publication_audit_records | Append-only audit trail |
| pubm_schema_migrations | Migration tracking |

---

## Validation

| Check | Result |
|---|---|
| All MVP-owned entities have tables | PASS |
| UUID primary keys (CHAR(36)) | PASS |
| Foreign keys on pubM-owned relations | PASS |
| No shared mutable cross-module tables | PASS |
| MariaDB 10.6 compatible (InnoDB, utf8mb4) | PASS |
| Migration idempotency via tracking table | PASS |

---

## Apply Migration

```bash
cp config/config.example.php config/config.php
# Edit database credentials
php scripts/migrate.php
```

Expected output:

```text
Migration 001_publications applied.
```

---

## Automated Test Validation

`tests/run.php` applies `tests/support/sqlite_schema.sql` and validates persistence through service layer.

```bash
php tests/run.php
```

Expected: 6 tests PASS (lifecycle, schedule, audit, persistence, uuid checks).
