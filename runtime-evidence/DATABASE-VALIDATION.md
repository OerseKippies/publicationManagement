# pubM Database Validation

Date: 2026-06-06
Host: 185.175.200.60 (Versio MariaDB 10.6)
Database: nol_module_pubm

---

## Migration Execution

Command:

```text
D:\Programs\PHP\php.exe scripts/migrate.php
```

Output:

```text
Migration 001_publications applied.
```

Re-run:

```text
Migration 001_publications already applied.
```

---

## Schema Tables

| Table | Present |
|---|---|
| pubm_schema_migrations | Yes |
| publications | Yes |
| publication_drafts | Yes |
| publication_templates | Yes |
| publication_channels | Yes |
| publication_targets | Yes |
| publication_schedules | Yes |
| publication_versions | Yes |
| publication_audit_records | Yes |

Validation script: `scripts/validate_schema.php`

---

## Indexes Verified

| Table | Indexes |
|---|---|
| publications | PRIMARY, idx_publications_status, idx_publications_source |
| publication_drafts | PRIMARY, uq_publication_drafts_publication, idx_publication_drafts_state |
| publication_schedules | PRIMARY, idx_publication_schedules_due, idx_publication_schedules_publication |
| publication_audit_records | PRIMARY, idx_publication_audit_entity, idx_publication_audit_correlation, idx_publication_audit_publication |

Full output: `runtime-evidence/schema-validation-output.json`

---

## Foreign Key Constraints

| Table | Constraint | References |
|---|---|---|
| publication_drafts | fk_publication_drafts_publication | publications |
| publication_targets | fk_publication_targets_publication | publications |
| publication_targets | fk_publication_targets_channel | publication_channels |
| publication_schedules | fk_publication_schedules_publication | publications |
| publication_versions | fk_publication_versions_publication | publications |

---

## Audit Table

`publication_audit_records` — append-only, indexed by entity and correlation.

---

## Result

**PASS** — schema, indexes, constraints, and audit tables verified on Versio MariaDB.
