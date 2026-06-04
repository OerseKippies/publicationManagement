# publicationManagement Governance

Status: Architecture Foundation
Authority: OK-Core

## Authority Order

1. OK-Core ADRs
2. OK-Core architecture documents
3. OK-Core governance documents
4. OK-Core inventories
5. OK-Core approval decisions and approved governance issues
6. Local pubM ADRs
7. Local pubM documentation

## Rules

- OK-Core is the single source of truth.
- Local pubM documentation may clarify pubM architecture but may not override OK-Core.
- pubM must not expand beyond approved module boundaries.
- pubM must not implement runtime code before OK-Core approval.
- Claims require evidence in repository documentation.
- Cross-module communication must pass through communicationLayer.
- pubM must not read or write foreign module databases.
- pubM must not own shared mutable tables.

## Mandatory OK-Core Evidence

- `architecture/MODULE-CATALOG.md`
- `architecture/MODULE-BOUNDARIES.md`
- `architecture/API-GOVERNANCE.md`
- `architecture/DEPLOYMENT-MATRIX.md`
- `architecture/DEPLOYMENT-STRATEGY.md`
- `architecture/HOSTING-ASSESSMENT-VERSIO.md`
- `governance/Architectural-Decision-Records/ADR-0011-No-Direct-Module-Coupling.md`
- `governance/Architectural-Decision-Records/ADR-0012-API-First-Architecture.md`
- `governance/Architectural-Decision-Records/ADR-0016-Identity-And-Access-Architecture.md`
- `governance/Architectural-Decision-Records/ADR-0019-VERSIO-FIRST-DEPLOYMENT-STRATEGY.md`
- `governance/Architectural-Decision-Records/ADR-0021-COMMUNICATIONLAYER-MANDATORY-BOUNDARY.md`
- `governance/Architectural-Decision-Records/ADR-0022-ADVERTISEMENT-VS-PUBLICATION-OWNERSHIP.md`
- OK-Core issue #4, `Governance baseline: Personal Data Protection v1`

## Implementation Gate

Implementation may start only after OK-Core approval of this architecture foundation and API direction.
