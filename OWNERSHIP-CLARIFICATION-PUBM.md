# pubM Ownership Clarification

Date: 2026-06-05
Authority: OK-Core
Source: OK-Core Issue #26
Status: Resolved

## Purpose

Clarify authoritative ownership of `PlatformExecution` and `PublicationSynchronization` per OK-Core MODULE-BOUNDARIES.md.

## Decision Summary

| Concept | Owner | Decision |
|---|---|---|
| PlatformExecution | publicationManagement (pubM) | A — pubM owns |
| PublicationSynchronization | publicationManagement (pubM) | A — pubM owns |

OK-Core MODULE-BOUNDARIES.md is authoritative. No ownership transfer to another module.

## PlatformExecution

| Field | Value |
|---|---|
| Owner module | publicationManagement (pubM) |
| Owner code | pubM |
| Decision | A — pubM owns |

**Justification**

OK-Core `architecture/MODULE-BOUNDARIES.md` lists `PlatformExecution` under publicationManagement owns. pubM owns publication execution and lifecycle. Platform execution is the runtime act of publishing to external platforms — a pubM responsibility, not adM or commL.

adManagement requests publication via `PublicationRequest` through commL. pubM executes and owns execution state.

**Boundary impact**

- adM must not execute platform actions or own execution records
- commL mediates transport only; does not own execution
- Platform execution HYBRID runtime (browser automation) is a pubM-owned extension per ADR-0026, excluded from MVP foundation implementation scope but not from ownership

## PublicationSynchronization

| Field | Value |
|---|---|
| Owner module | publicationManagement (pubM) |
| Owner code | pubM |
| Decision | A — pubM owns |

**Justification**

OK-Core `architecture/MODULE-BOUNDARIES.md` lists `PublicationSynchronization` under publicationManagement owns. Synchronization of publication state with external platforms is part of publication lifecycle governance owned by pubM.

**Boundary impact**

- adM may read returned status through commL contracts but does not own synchronization state
- syncManagement owns import/sync infrastructure, not publication lifecycle truth
- pubM owns synchronization records as part of publication execution lifecycle

## Related Canonical Entities (Aligned)

Also owned by pubM per MODULE-BOUNDARIES:

```text
PublicationActivation
PublicationArchiving
PlatformFormFilling
RecorderExecution
PublicationHistory
```

## Evidence

| Source | Path |
|---|---|
| OK-Core boundaries | OK-Core/architecture/MODULE-BOUNDARIES.md (pubM section) |
| OK-Core ownership matrix | OK-Core/architecture/OWNERSHIP-MATRIX.md |
| OK-Core ADR | ADR-0022-ADVERTISEMENT-VS-PUBLICATION-OWNERSHIP.md |
| OK-Core ADR | ADR-0026-PUBM-HYBRID-DEPLOYMENT-CLASSIFICATION.md |
| OK-Core ADR | ADR-0027-PUBM-OWNERSHIP-CLARIFICATION.md |
| Local scope | publicationManagement/MODULE-SCOPE.md |
| Local matrix | publicationManagement/architecture/OWNERSHIP-MATRIX.md |

## Remediation

Resolves RISK-001 (EGA-PR002). Local MODULE-SCOPE extended to match OK-Core canonical ownership. No MODULE-BOUNDARIES change required.
