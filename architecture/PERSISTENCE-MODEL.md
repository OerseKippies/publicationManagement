# pubM Persistence Model

Status: Architecture Foundation

## Database Strategy

pubM uses a module-owned MariaDB schema compatible with MariaDB 10.6.

## Draft Tables

| Table | Owned Data |
|---|---|
| pubm_publications | Publication lifecycle anchor |
| pubm_publication_drafts | Draft snapshots |
| pubm_publication_templates | Template definitions |
| pubm_publication_channels | Channel configuration records |
| pubm_publication_targets | Target references |
| pubm_publication_schedules | Schedule records |
| pubm_publication_versions | Immutable version snapshots |
| pubm_publication_status_history | Status history |
| pubm_publication_audit_records | Immutable mutation audit |
| pubm_schema_migrations | pubM schema migration history |

## JSON Registries

No JSON registry is required for MVP foundation. If a lightweight registry is later needed for non-sensitive local configuration, it must not replace MariaDB as the system of record and must be approved before implementation.

## Retention Strategy

- PublicationAuditRecord entries are retained for the life of the publication record unless OK-Core approves a retention period.
- PublicationVersion records are retained for history and rollback evidence.
- Archived and retired publications remain queryable for audit/history.

## Backup Strategy

MariaDB backups follow the hosting backup capability documented by OK-Core Versio evidence. A production implementation must define backup frequency and restore testing before live use.

## Archive Strategy

Archive means removing a publication from active workflows while preserving versions, schedules, statuses and audit records.

## Schema Ownership Validation

pubM tables contain only pubM-owned data. Foreign module references are stored as IDs and source-module markers. Foreign snapshots may be stored for audit/history only and must not become source of truth.
