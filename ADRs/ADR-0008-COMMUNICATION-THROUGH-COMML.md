# ADR-0008 - pubM Communication Through commL

Status: Accepted — aligned with OK-Core Architecture Foundation approval (APR-2026-06-05-006)
Date: 2026-06-04

## Decision

pubM communicates with all other modules through communicationLayer.

Required:

```text
pubM -> communicationLayer (commL) -> target module
source module -> communicationLayer (commL) -> pubM
```

Forbidden:

```text
pubM -> module
pubM -> foreign database
pubM -> foreign internal code
shared mutable tables
```

## Rationale

communicationLayer owns routing, mediation and contract registry behavior. pubM owns publication lifecycle data only.

## Evidence

- OK-Core ADR-0021
- OK-Core `architecture/MODULE-BOUNDARIES.md`
- `architecture/COMMUNICATION-BOUNDARY.md`
