# pubM MariaDB Schema Draft

Status: Implemented — see schemas/001_publications.sql
Target: MariaDB 10.6

## Ownership Rule

All tables are pubM-owned. Foreign module records are referenced only by IDs and source module names.

## Draft Table Set

```sql
pubm_publications
pubm_publication_drafts
pubm_publication_templates
pubm_publication_channels
pubm_publication_targets
pubm_publication_schedules
pubm_publication_versions
pubm_publication_status_history
pubm_publication_audit_records
pubm_schema_migrations
```

## Draft Columns

### pubm_publications

- publication_id
- source_module
- source_object_id
- current_status
- active_version_id
- created_at
- updated_at

### pubm_publication_audit_records

- audit_record_id
- entity_type
- entity_id
- action
- previous_state
- new_state
- actor_module
- actor_id_ref
- correlation_id
- reason
- created_at

## Shared Tables

Shared mutable tables are forbidden.

## Implementation Gate

Runtime schema implemented in `schemas/001_publications.sql`. Apply via `scripts/migrate.php`.
