# publicationManagement (pubM)

Repository: OerseKippies/publicationManagement
Module code: pubM
Governance authority: OK-Core
Status: MVP v1 Architecture Foundation candidate

## Purpose

publicationManagement owns the lifecycle of publications in the OK ecosystem.

pubM records publication drafts, templates, channel configuration, targets, schedules, versions, statuses and immutable audit history. It does not own advertisements, marketplace integrations, communication routing, identity, master data, inventory, sales or external API gateway behavior.

## Governance Authority

OK-Core is the single source of truth. Local pubM documentation is subordinate to:

1. OK-Core ADRs
2. OK-Core architecture documents
3. OK-Core governance documents
4. OK-Core inventories
5. OK-Core approval decisions and approved governance issues
6. Local pubM ADRs
7. Local pubM documentation

Primary evidence:

- OK-Core `architecture/MODULE-CATALOG.md`
- OK-Core `architecture/MODULE-BOUNDARIES.md`
- OK-Core `architecture/API-GOVERNANCE.md`
- OK-Core `architecture/DEPLOYMENT-MATRIX.md`
- OK-Core `architecture/DEPLOYMENT-STRATEGY.md`
- OK-Core `architecture/HOSTING-ASSESSMENT-VERSIO.md`
- OK-Core `governance/Architectural-Decision-Records/ADR-0021-COMMUNICATIONLAYER-MANDATORY-BOUNDARY.md`
- OK-Core `governance/Architectural-Decision-Records/ADR-0022-ADVERTISEMENT-VS-PUBLICATION-OWNERSHIP.md`

## Owns

- Publication
- PublicationDraft
- PublicationTemplate
- PublicationChannel
- PublicationTarget
- PublicationSchedule
- PublicationVersion
- PublicationStatus
- PublicationAuditRecord

## Does Not Own

- User, Role, Permission, AccessPolicy or ServiceAccount
- Breed definitions, product definitions or canonical identifiers
- Stock, inventory movement or reservation ownership
- Orders, customers or invoices
- Advertisements, advertisement pricing or advertisement business rules
- Communication routing, service mediation or communication contracts
- Direct marketplace integrations or external API gateway ownership

## Communication Rule

Required:

```text
Module -> communicationLayer (commL) -> Module
```

Forbidden:

```text
Module -> Module
Module -> foreign database
Module -> foreign internal code
shared mutable tables
```

## Deployment Baseline

The MVP foundation targets VERSIO_HOSTED architecture compatibility:

```text
PHP 8.3
MariaDB 10.6
HTTPS
Cron
SSH
Git deployment
```

The foundation excludes Node.js runtime, npm runtime, Docker, RabbitMQ, Redis, Python runtime dependencies, WebSockets and long-running daemons.

## Implementation Status

No runtime implementation is approved in this repository until OK-Core grants implementation approval.

This repository currently contains architecture, governance, research, contracts, validation and handover documentation only.
