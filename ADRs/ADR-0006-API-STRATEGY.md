# ADR-0006 - pubM API Strategy

Status: Accepted locally, pending OK-Core approval
Date: 2026-06-04

## Decision

pubM maintains a non-implementation API draft in the module repository until OK-Core review.

Draft artifacts:

- `contracts/API-DESIGN-NOTES.md`
- `contracts/API-OWNERSHIP.md`
- `contracts/API-VERSIONING-STRATEGY.md`
- `contracts/API-ERROR-MODEL.md`
- `public/api/publication-api-draft.yaml`

## Rules

- API status remains DRAFT_IN_MODULE.
- Canonical accepted API contracts belong in OK-Core.
- All cross-module access must route through communicationLayer.
- No runtime implementation starts before OK-Core approval.

## Evidence

- OK-Core `architecture/API-GOVERNANCE.md`
- OK-Core ADR-0012
- OK-Core ADR-0021
