# pubM Architecture

Status: MVP v1 Architecture Foundation candidate
Date: 2026-06-04
Authority: OK-Core

## Architecture Summary

publicationManagement is the OK module responsible for publication lifecycle governance. It owns publication records, drafts, templates, channel configuration, targets, schedules, versions, statuses and immutable audit records.

pubM is not a marketplace integration module and not a communication gateway. Cross-module communication is mediated by communicationLayer.

## Foundation Decisions

| Decision | Document |
|---|---|
| Ownership boundaries | `ADRs/ADR-0001-OWNERSHIP-BOUNDARIES.md` |
| Publication domain model | `ADRs/ADR-0002-PUBLICATION-DOMAIN-MODEL.md` |
| Lifecycle strategy | `ADRs/ADR-0003-PUBLICATION-LIFECYCLE-STRATEGY.md` |
| Persistence strategy | `ADRs/ADR-0004-PERSISTENCE-STRATEGY.md` |
| Audit strategy | `ADRs/ADR-0005-AUDIT-STRATEGY.md` |
| API strategy | `ADRs/ADR-0006-API-STRATEGY.md` |
| Scheduling strategy | `ADRs/ADR-0007-SCHEDULING-STRATEGY-VERSIO.md` |
| commL communication | `ADRs/ADR-0008-COMMUNICATION-THROUGH-COMML.md` |

## Domain Model

Domain model documents:

- `architecture/domain-models/PUBLICATION-DOMAIN-MODEL.md`
- `architecture/MODULE-INVENTORY.md`
- `MODULE-SCOPE.md`

Core entities:

```text
Publication
PublicationDraft
PublicationTemplate
PublicationChannel
PublicationTarget
PublicationSchedule
PublicationVersion
PublicationStatus
PublicationAuditRecord
```

## Lifecycle Model

State model documents:

- `architecture/state-models/PUBLICATION-STATE-MODEL.md`
- `architecture/state-models/PUBLICATION-DRAFT-STATE-MODEL.md`
- `architecture/state-models/PUBLICATION-SCHEDULE-STATE-MODEL.md`

Publication lifecycle:

```text
DRAFT -> REVIEW -> APPROVED -> SCHEDULED -> PUBLISHED -> ARCHIVED -> RETIRED
```

## Communication

Required:

```text
Module -> communicationLayer (commL) -> Module
```

pubM may consume publication requests, identity context and reference data only through approved communicationLayer contracts.

## Persistence

Persistence target:

```text
MariaDB 10.6
```

Persistence governance:

- `architecture/PERSISTENCE-MODEL.md`
- `schemas/MARIADB-SCHEMA-DRAFT.md`

No shared mutable tables are allowed.

## Security And Audit

Security and audit documents:

- `architecture/SECURITY-MODEL.md`
- `architecture/AUDIT-MODEL.md`

Authentication and authorization are assumed to be provided by identityManagement through communicationLayer. pubM stores actor references and immutable audit records.

## Deployment

OK-Core classification: **HYBRID** (Accepted — ADR-0026, APR-2026-06-05-012)

MVP foundation covers the VERSIO_HOSTED core only:

```text
PHP 8.3
MariaDB 10.6
HTTPS
Cron
SSH
Git deployment
```

Platform execution (browser automation, recorder, marketplace connectors) is a separately approved HYBRID extension and is excluded from this foundation.

See `architecture/DEPLOYMENT.md`.
