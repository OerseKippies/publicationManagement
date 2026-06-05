# pubM Module Scope

Status: Architecture Foundation
Module: publicationManagement (pubM)
Authority: OK-Core

## Owns

| Entity | Ownership Reason | Evidence |
|---|---|---|
| Publication | Core lifecycle object governed by pubM | OK-Core ADR-0022, MODULE-BOUNDARIES |
| PublicationDraft | Draft state before approval/publication | Local ADR-0002, state models |
| PublicationTemplate | pubM-owned template for publication preparation | Local ADR-0002 |
| PublicationChannel | Channel configuration within pubM boundary | Local ADR-0002 |
| PublicationTarget | Publication destination reference without integration ownership | Local ADR-0002 |
| PublicationSchedule | Timing and scheduled intent | Local ADR-0004, ADR-0007 |
| PublicationVersion | Preserved publication version history | Local ADR-0002 |
| PublicationStatus | Lifecycle status and transition governance | Local ADR-0003 |
| PublicationAuditRecord | Immutable mutation evidence | Local ADR-0005 |
| PublicationActivation | Activation state within publication lifecycle | OK-Core MODULE-BOUNDARIES |
| PublicationArchiving | Archiving state within publication lifecycle | OK-Core MODULE-BOUNDARIES |
| PublicationSynchronization | Publication state sync with external platforms | OK-Core MODULE-BOUNDARIES, Issue #26 |
| PlatformExecution | Platform publication execution records | OK-Core MODULE-BOUNDARIES, Issue #26 |
| PlatformFormFilling | Platform form execution (HYBRID extension) | OK-Core MODULE-BOUNDARIES |
| RecorderExecution | Recorder-based execution (HYBRID extension) | OK-Core MODULE-BOUNDARIES |
| PublicationHistory | Immutable publication history | OK-Core MODULE-BOUNDARIES |

## Allowed Capabilities

- Publication lifecycle management
- Publication scheduling metadata
- Publication status tracking
- Publication versioning
- Publication templates
- Publication channel configuration
- Publication history
- Publication audit trail
- Publication activation and archiving
- Publication synchronization with external platforms
- Platform execution governance (HYBRID runtime extension per ADR-0026)

## Explicit Non-Ownership

| Concept | Owner / Reason |
|---|---|
| User, Role, Permission, AccessPolicy, ServiceAccount | identityManagement |
| Breed definitions, product definitions, canonical identifiers | masterDataManagement |
| Stock, inventory movements, inventory reservations | inventoryManagement |
| Orders, customers, invoices | salesManagement / relationshipManagement / financeManagement |
| Advertisements, advertisement pricing, advertisement business rules | adManagement |
| Communication routing, service mediation, contracts | communicationLayer |
| Direct marketplace integrations, external API gateway ownership | communicationLayer / separately approved connectors |
| Platform execution runtime workers | HYBRID extension — owned by pubM, implementation out of MVP foundation scope |

## Boundary Rule

pubM may store foreign references and immutable snapshots for audit/history, but those snapshots are never source of truth.

Required access pattern:

```text
pubM -> communicationLayer (commL) -> owning module
```
