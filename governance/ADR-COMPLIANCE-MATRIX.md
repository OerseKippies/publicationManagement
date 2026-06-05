# pubM ADR Compliance Matrix

Date: 2026-06-05
Module: publicationManagement (pubM)
Scope: Accepted OK-Core ADRs affecting pubM

---

## Module Ownership and Boundaries

| ADR | Requirement | Implemented | Partially Implemented | Missing | Evidence |
|---|---|---|---|---|---|
| ADR-0022 Advertisement vs Publication | pubM owns publication lifecycle; adM requests only | Yes | — | — | ADRs/ADR-0001, MODULE-SCOPE.md, OWNERSHIP-CLARIFICATION-PUBM.md |
| ADR-0027 pubM Ownership Clarification | PlatformExecution and PublicationSynchronization owned by pubM | Yes | — | — | OWNERSHIP-CLARIFICATION-PUBM.md, MODULE-SCOPE.md |

---

## Communication

| ADR | Requirement | Implemented | Partially Implemented | Missing | Evidence |
|---|---|---|---|---|---|
| ADR-0021 commL Mandatory Boundary | All cross-module communication via commL | Yes | — | — | ADRs/ADR-0008, architecture/COMMUNICATION-BOUNDARY.md |
| ADR-0011 No Direct Module Coupling | No direct module/database coupling | Yes | — | — | architecture/COMMUNICATION-BOUNDARY.md, governance/README.md |

---

## Deployment

| ADR | Requirement | Implemented | Partially Implemented | Missing | Evidence |
|---|---|---|---|---|---|
| ADR-0019 Versio-First Deployment | Versio-compatible deployment baseline | Yes | — | — | architecture/DEPLOYMENT.md, ADRs/ADR-0007 |
| ADR-0026 pubM HYBRID Classification | Core VERSIO_HOSTED; platform execution HYBRID extension | Yes | — | — | architecture/DEPLOYMENT.md, OK-Core APR-2026-06-05-012 |

---

## API and Persistence

| ADR | Requirement | Implemented | Partially Implemented | Missing | Evidence |
|---|---|---|---|---|---|
| ADR-0012 API-First Architecture | API draft in module; canonical promotion via OK-Core | Yes | — | — | ADRs/ADR-0006, contracts/, public/api/ |
| ADR-0016 Identity and Access | Identity via idM through commL | Yes | — | — | architecture/SECURITY-MODEL.md |

---

## Governance, Approval, and Reporting

| ADR | Requirement | Implemented | Partially Implemented | Missing | Evidence |
|---|---|---|---|---|---|
| ADR-CORE-0030 Repository Reporting | GitHub evidence; reviews/; approval requests | — | Yes | — | reviews/, approval-request/ created by remediation; prior cycle lacked structure |
| ADR-CORE-0031 Required Documentation Registry | Registry-driven documentation compliance | Yes | — | — | compliance/, governance/REGISTRY-COMPLIANCE-MATRIX.md |
| ADR-CORE-0032 Registry Consumption | Consumption log and MODULE-COMPLIANCE | Yes | — | — | compliance/DOCUMENTATION-REGISTRY-CONSUMPTION-LOG.md |
| ADR-CORE-0033 End-to-End Approval Lifecycle | RFA → evidence → review → closure audit | — | Yes | — | approval-request/RFA-GOVERNANCE-ALIGNMENT.md prepared; not submitted |
| ADR-CORE-0034 Mandatory Reading Compliance | Reading consumption log required | — | Yes | — | compliance/MANDATORY-READING-CONSUMPTION-LOG.md; historical unavailable |
| ADR-0030 RFA Response Standard | Formal RFA submission structure | — | Yes | — | approval-request/RFA-GOVERNANCE-ALIGNMENT.md prepared |

---

## Local pubM ADRs (Aligned with OK-Core Foundation Approval)

| ADR | Requirement | Implemented | Partially Implemented | Missing | Evidence |
|---|---|---|---|---|---|
| ADR-0001 Ownership Boundaries | Local ownership aligned with MODULE-BOUNDARIES | Yes | — | — | ADRs/ADR-0001 |
| ADR-0002 Publication Domain Model | Domain model documented | Yes | — | — | architecture/domain-models/ |
| ADR-0003 Publication Lifecycle | State transitions documented | Yes | — | — | architecture/state-models/ |
| ADR-0004 Persistence Strategy | MariaDB module-owned schema | Yes | — | — | architecture/PERSISTENCE-MODEL.md |
| ADR-0005 Audit Strategy | Append-only audit | Yes | — | — | architecture/AUDIT-MODEL.md |
| ADR-0006 API Strategy | Draft-in-module strategy | Yes | — | — | contracts/ |
| ADR-0007 Scheduling Versio | Cron-compatible scheduling | Yes | — | — | architecture/DEPLOYMENT.md |
| ADR-0008 Communication through commL | commL-only routing | Yes | — | — | architecture/COMMUNICATION-BOUNDARY.md |

---

## Summary

| Category | Implemented | Partially Implemented | Missing |
|---|---|---|---|
| Ownership / boundaries | 2 | 0 | 0 |
| Communication | 2 | 0 | 0 |
| Deployment | 2 | 0 | 0 |
| API / persistence | 2 | 0 | 0 |
| Governance baseline (0030–0034) | 2 | 4 | 0 |
| Local pubM ADRs | 8 | 0 | 0 |

**ADR compliance: 18/22 fully implemented (82%); 4 partially implemented (approval lifecycle artifacts prepared, pending submission and govM verification).**

No ADR requirements are fully missing. Partial items reflect process completion pending external verification.
