# pubM Compliance Matrix

Date: 2026-06-05
Repository: OerseKippies/publicationManagement
Module: publicationManagement (pubM)
Module Type: Runtime Module
Registry Version: 1.0.0
Authority: OK-Core `governance/REQUIRED-DOCUMENTATION-REGISTRY.md`

## Matrix Legend

| Column | Meaning |
|---|---|
| Required | Registry or governance rule status |
| Present | File or artifact exists in pubM repository |
| Missing | Gap identified |
| Compliant | Meets current baseline requirement |

---

## Runtime Module — Documentation Registry

| Document / Path | Required | Present | Missing | Compliant |
|---|---|---|---|---|
| README.md | REQUIRED | Yes | — | **Yes** |
| ARCHITECTURE.md | REQUIRED | Yes | — | **Yes** |
| MODULE-SCOPE.md | REQUIRED | Yes | — | **Yes** |
| CHANGELOG.md | OPTIONAL | Yes | — | N/A |
| ADRs/ | REQUIRED | Yes (8 local ADRs) | — | **Yes** |
| handover/ | CONDITIONAL (RFA submission) | Yes | — | **Yes** |
| architecture/DOD-VALIDATION.md | CONDITIONAL (MVP approval) | Yes | — | **Partial** — stale post-approval |
| compliance/MODULE-COMPLIANCE.md | REQUIRED (after registry adoption) | No | Yes | **No** |
| compliance/DOCUMENTATION-REGISTRY-CONSUMPTION-LOG.md | REQUIRED (after registry adoption) | No | Yes | **No** |
| contracts/ or docs/api/ | REQUIRED | Yes (`contracts/`, `public/api/`) | — | **Yes** |
| schemas/ or architecture persistence docs | CONDITIONAL (before persistence impl) | Yes | — | **Yes** (pre-implementation) |
| architecture/OWNERSHIP-NAMING-MAP.md | CONDITIONAL (when mapping ADR exists) | No | — | **N/A** — resolved via ADR-0027 |

**Registry documentation score: 8/11 required items compliant (73%).**  
Two REQUIRED compliance files missing; one CONDITIONAL item stale.

---

## Mandatory Reading Compliance (ADR-CORE-0034)

| Document / Path | Required | Present | Missing | Compliant |
|---|---|---|---|---|
| compliance/MANDATORY-READING-CONSUMPTION-LOG.md | REQUIRED | No | Yes | **No** |
| MANDATORY-READ-MATERIAL.md consumed (logged) | REQUIRED | No log | Yes | **No** |
| MODULE-TYPE reading consumed (logged) | REQUIRED | No log | Yes | **No** |
| REQUIRED-DOCUMENTATION-REGISTRY.md consumed (logged) | REQUIRED | No log | Yes | **No** |
| APPROVAL-PROCESS.md consumed (logged) | REQUIRED | No log | Yes | **No** |
| REVIEW-STANDARD.md consumed (logged) | REQUIRED | No log | Yes | **No** |
| Conditional ADRs 0022, 0026, 0027 consumed (logged) | CONDITIONAL | No log | Yes | **No** |

**Reading compliance score: 0/7 (0%).**

---

## Reporting Standard Compliance (ADR-CORE-0030)

| Artifact | Required | Present | Missing | Compliant |
|---|---|---|---|---|
| architecture/ | For architecture work | Yes | — | **Yes** |
| handover/ | Major milestones | Yes | — | **Yes** (foundation only) |
| governance/ | Governance work | Partial | Gap assessment (this review) | **Partial** |
| reviews/ | Formal reviews | No | Yes | **No** |
| approval-request/ | Approval requests | No | Yes | **No** |
| reviews/evidence/<RFA-ID>/ | RFA evidence packages | No | Yes | **No** |
| docs/runtime-evidence/ | MVP Runtime (future) | No | N/A pre-impl | N/A |
| audit/ | Finding records | No | Yes | **No** |
| GitHub evidence path format | All claims | Partial | Commit hashes sparse | **Partial** |

**Reporting compliance score: 3/7 applicable items (43%).**

---

## Approval Process Compliance (ADR-CORE-0033)

| Check | Required | Present | Missing | Compliant |
|---|---|---|---|---|
| Architecture Foundation approval record | Yes | Yes (OK-Core APR-006) | — | **Yes** |
| Deployment classification record | Yes | Yes (OK-Core APR-012) | — | **Yes** |
| RFA registered in OK-Core `rfas/` | For current baseline | No | Yes | **No** |
| Evidence resolution package | Before review | No | Yes | **No** |
| Module review report | Before decision | No | Yes | **No** |
| Closure audit | Before lifecycle closed | No | Yes | **No** |
| Registry compliance gate | Before next approval | No | Yes | **No** |
| Reading compliance gate | Before next approval | No | Yes | **No** |
| govM verification | Before next approval | No | Yes | **No** |

**Approval process score: 2/9 for next-cycle requirements (22%).**

---

## Architecture Content Compliance (Retained from Foundation Review)

| Area | Required | Present | Compliant |
|---|---|---|---|
| Ownership matrix | Yes | Yes | **Yes** |
| Non-ownership matrix | Yes | Yes | **Yes** |
| Domain models | Yes | Yes | **Yes** |
| State models | Yes | Yes | **Yes** |
| Communication boundary | Yes | Yes | **Yes** |
| Persistence model | Yes | Yes | **Yes** |
| Security model | Yes | Yes | **Yes** |
| Audit model | Yes | Yes | **Yes** |
| Deployment model | Yes | Yes | **Yes** |
| Dependency graph | Yes | Yes | **Yes** |
| Research register | Yes | Yes | **Yes** |
| API draft | Yes | Yes | **Yes** |

**Architecture content score: 12/12 (100%).**

---

## ADR Adoption Matrix

| ADR | Topic | Required | Present | Missing | Compliant |
|---|---|---|---|---|---|
| ADR-0011 | No direct coupling | Accepted | Referenced | — | **Yes** |
| ADR-0012 | API-first | Accepted | ADR-0006 | — | **Yes** |
| ADR-0016 | Identity/access | Accepted | SECURITY-MODEL | — | **Yes** |
| ADR-0019 | Versio-first deployment | Accepted | DEPLOYMENT.md | — | **Yes** |
| ADR-0021 | commL boundary | Accepted | ADR-0008 | — | **Yes** |
| ADR-0022 | Ad vs publication | Accepted | ADR-0001 | — | **Yes** |
| ADR-0026 | HYBRID deployment | Accepted | DEPLOYMENT.md | — | **Yes** |
| ADR-0027 | Ownership clarification | Accepted | OWNERSHIP-CLARIFICATION | — | **Yes** |
| ADR-CORE-0030 | Reporting standard | Accepted | Partial | reviews/, RFA | **Partial** |
| ADR-CORE-0031 | Documentation registry | Accepted | No | compliance files | **No** |
| ADR-CORE-0032 | Registry consumption | Accepted | No | consumption log | **No** |
| ADR-CORE-0033 | Approval lifecycle | Accepted | Partial | RFA, closure audit | **Partial** |
| ADR-CORE-0034 | Reading compliance | Accepted | No | reading log | **No** |

**ADR adoption score: 8/13 fully compliant (62%); 2 partial; 3 missing.**

---

## Governance Decision Matrix

| Decision | Affected Area | Implemented | Gap | Compliant |
|---|---|---|---|---|
| GD-2026-06-05-ECOSYSTEM-BASELINE | Registry adoption | No | All compliance files | **No** |
| GD-2026-06-05-ECOSYSTEM-BASELINE | Implementation readiness | Partial | Governance gate open | **Partial** |
| GD-2026-06-05-ECOSYSTEM-BASELINE | Issue #28 API deferral | Yes | Documented | **Yes** |
| GD-2026-06-05-ECOSYSTEM-BASELINE | Issue #26 ownership | Yes | OWNERSHIP-CLARIFICATION | **Yes** |
| GD-2026-06-05-MDM-BOUNDARY-ALIGNMENT | mdM consumption | Yes | NON-OWNERSHIP-MATRIX | **Yes** |

**Governance decision score: 3/5 (60%).**

---

## govM Verification Matrix

| Check | Required | Present | Compliant |
|---|---|---|---|
| Documentation verification chain | Before next approval | Not started | **No** |
| Reading verification chain | Before next approval | Not started | **No** |
| govM audit report in govM repo | Adoption Phase D | None found | **No** |

---

## Overall Compliance Summary

| Domain | Weight | Score |
|---|---|---|
| Architecture content | 25% | 100% |
| Documentation registry | 25% | 73% |
| Reading compliance | 15% | 0% |
| Reporting standard | 15% | 43% |
| Approval process (next cycle) | 10% | 22% |
| ADR adoption | 10% | 62% |

**Weighted overall governance compliance: approximately 48%.**

---

## Non-Compliant Items Requiring Remediation

Priority order:

1. `compliance/MODULE-COMPLIANCE.md`
2. `compliance/DOCUMENTATION-REGISTRY-CONSUMPTION-LOG.md`
3. `compliance/MANDATORY-READING-CONSUMPTION-LOG.md`
4. `reviews/` structure with RFA evidence package
5. `approval-request/` RFA submission
6. Status document sync (DOD, README, handover, ACTIVE-WORK)
7. Local ADR status alignment with OK-Core acceptance
8. govM verification request
9. OK-Core Governance Alignment RFA and approval record
