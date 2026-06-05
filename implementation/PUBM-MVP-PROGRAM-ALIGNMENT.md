# pubM MVP Program Alignment

Date: 2026-06-06
Module: publicationManagement (pubM)
Program: MVP-ACCELERATION-001
Authority: OK-Core/implementation/MVP-ACCELERATION-PROGRAM.md

---

## Program Position

| Field | Value |
|---|---|
| MVP scope | **In scope** — OK-Core/implementation/MVP-SCOPE.md |
| Build order phase | **Phase 2** — publicationManagement |
| Current ecosystem phase | Phase 1 invM **complete** (MVP Runtime APPROVED APR-2026-06-06-022) |
| pubM status | Architecture approved — **Phase 2 build next** |
| Mode | Build first, document while building |

---

## Build Order Context

Per OK-Core/implementation/IMPLEMENTATION-BUILD-ORDER.md:

| Phase | Focus | pubM Role |
|---|---|---|
| Foundation | commL, idM, mdM | Consume only — approved |
| **1** | invM | Dependency — MVP Runtime APPROVED |
| **2** | **pubM** | **Publication lifecycle build** |
| 3 | invM → pubM integration | Integration tests + commL routes |
| 4 | pubM → adM integration | Advertisement from publication |
| 5 | End-to-end MVP | Full vertical slice |
| 6 | MVP Readiness Audit | govM verification — MVP modules only |

---

## Vertical Slice Role

Per OK-Core/implementation/FIRST-VERTICAL-SLICE-DECISION.md and MVP-SCOPE:

```text
Inventory Item (invM)
  → Master Data Validation (mdM via commL)
    → Publication Request (pubM)
      → Advertisement (adM)
        → Audit Trail (module-owned records)
```

pubM owns:

- Publication Request acceptance
- Publication lifecycle states
- Publication History
- Publication audit records (module-owned)

pubM does **not** own in MVP:

- HYBRID platform execution runtime
- Centralized audit repository
- Inventory or advertisement business logic

---

## Dependencies

### Foundation (consume — do not rebuild)

| Module | Requirement |
|---|---|
| commL | Contract routing; pubM routes registered in MVP registry |
| idM | Actor context, service accounts, permissions via commL |
| mdM | Master data validation before publication acceptance via commL |

### Phase 1 complete

| Module | Status |
|---|---|
| invM | MVP Runtime APPROVED — inventory API available for Phase 3 integration |

### Phase 2 peers

| Module | Relationship |
|---|---|
| adM | Phase 4 consumer of publication events/requests via commL |
| invM | Phase 3 upstream — publication requests from inventory context |

### Hard gates (IMPLEMENTATION-BUILD-ORDER)

- G1: No cross-module integration without commL runtime
- G2: No secured API without idM service accounts
- G3: No publication acceptance without mdM validation via commL

---

## Phase 2 Required Runtime Outcomes

Per OK-Core/implementation/MVP-ACCELERATION-PROGRAM.md Phase 2:

| Deliverable | Required |
|---|---|
| Publication Request | YES |
| Publication State | YES |
| Publication History | YES |
| Audit | YES |
| Persistence (MariaDB) | YES |
| commL-mediated API | YES |

Explicitly **out of MVP Phase 2 scope:**

- HYBRID platform execution workers
- Canonical API promotion to OK-Core (deferred Issue #28)
- Direct module coupling

---

## Runtime Evidence Expectations

Per OK-Core/governance/REPOSITORY-REPORTING-STANDARD.md and MVP-ACCELERATION-PROGRAM:

Location: `docs/runtime-evidence/` (create during build)

| Evidence Type | Required |
|---|---|
| Endpoint evidence | Publication CRUD / lifecycle transitions |
| Migration evidence | MariaDB schema deployment |
| Audit evidence | Append-only publication audit records |
| Correlation evidence | idM actor references via commL |
| Deployment evidence | VERSIO_HOSTED MVP core compatibility |
| Manual verification evidence | Lifecycle state transition proof |
| Integration readiness | commL contract consumption (Phase 3 prep) |

---

## Mandatory Documentation During Build (Only)

Per START-HERE §7 and MVP-ACCELERATION-PROGRAM:

| Deliverable | When |
|---|---|
| Runtime Evidence | During Phase 2 build |
| DoD (`architecture/DOD-VALIDATION.md`) | Update at MVP Runtime checkpoint |
| Review | `reviews/REVIEW-pubm-CHECKPOINT-PHASE2.md` |
| Handover | Phase 2 completion handover |

**Do not** create additional governance documents unless impacted by build.

---

## Handover Expectations

At Phase 2 checkpoint / MVP Runtime submission:

| Item | Content |
|---|---|
| Handover | Publication lifecycle runtime summary |
| DoD | MVP Runtime Complete section PASS with evidence refs |
| Review | Checkpoint max 2 pages per CHECKPOINT-REVIEW-TEMPLATE |
| Runtime evidence index | docs/runtime-evidence/ with GitHub paths |
| RFA | MVP Runtime Complete (not Governance Alignment) |

Template: OK-Core/implementation/CHECKPOINT-REVIEW-TEMPLATE.md

---

## Approval Trigger

| Milestone | When | Authority |
|---|---|---|
| MVP Runtime Complete | After Phase 2 build + evidence | OK-Core approval per START-HERE §8 |
| govM review | At MVP Runtime Complete claim | GOVM-MVP-REVIEW-POLICY.md |

---

## Deployment Alignment

| Component | MVP Phase 2 |
|---|---|
| pubM MVP core | VERSIO_HOSTED (PHP, MariaDB, Cron) |
| Platform execution HYBRID | **Deferred** — ADR-0026 |
| Deployment classification | APR-012 remains valid |

---

## Related

- governance/PUBM-APPROVAL-STATUS-MATRIX.md
- governance/PUBM-NEXT-MILESTONE-DECISION.md
- OK-Core/implementation/MVP-READINESS-CRITERIA.md
