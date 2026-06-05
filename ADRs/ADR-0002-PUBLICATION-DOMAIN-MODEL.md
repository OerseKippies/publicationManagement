# ADR-0002 - Publication Domain Model

Status: Accepted — aligned with OK-Core Architecture Foundation approval (APR-2026-06-05-006)
Date: 2026-06-04

## Decision

pubM models publication as a lifecycle aggregate with draft, template, channel, target, schedule, version, status and audit records.

## Model

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

## Rationale

This model preserves history and lifecycle state without owning advertisement content, customer data, marketplace integration logic or communication mediation.

## Evidence

- `architecture/domain-models/PUBLICATION-DOMAIN-MODEL.md`
- `MODULE-SCOPE.md`
- OK-Core ADR-0022
