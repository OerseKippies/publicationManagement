# ADR-0003 - Publication Lifecycle Strategy

Status: Accepted locally, pending OK-Core approval
Date: 2026-06-04

## Decision

pubM governs publication lifecycle through explicit states and transition rules.

Publication states:

```text
DRAFT
REVIEW
APPROVED
SCHEDULED
PUBLISHED
ARCHIVED
RETIRED
```

## Rationale

Explicit transitions prevent invalid publication mutations, preserve auditability and keep status ownership inside pubM.

## Evidence

- `architecture/state-models/PUBLICATION-STATE-MODEL.md`
- `architecture/state-models/PUBLICATION-DRAFT-STATE-MODEL.md`
- `architecture/state-models/PUBLICATION-SCHEDULE-STATE-MODEL.md`
