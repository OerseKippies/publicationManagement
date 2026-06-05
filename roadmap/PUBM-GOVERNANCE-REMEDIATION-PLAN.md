# pubM Governance Remediation Plan

Date: 2026-06-05
Repository: OerseKippies/publicationManagement
Module: publicationManagement (pubM)
Plan Type: Post-Approval Governance Alignment Remediation
Authority: OK-Core governance baseline v1.0.0 (EPIC #30)
Target State: Governance Alignment Readiness → Governance Alignment Approved

## Scope Boundaries

This plan addresses **governance and compliance alignment only**.

Explicitly excluded:

- Runtime implementation
- Business functionality expansion
- Module ownership boundary changes
- Architecture decision modifications (no conflicts identified)

---

## Current State

```text
FROM: Architecture Foundation Approved (APR-2026-06-05-006)
TO:   Governance Alignment Readiness (intermediate)
THEN: Governance Alignment Approved
THEN: MVP Ready For Implementation (future RFA)
```

Compliance baseline: **~48%** (see `governance/PUBM-APPROVAL-READINESS-REPORT.md`)

---

## Remediation Phases

### Phase 1 — Compliance File Creation

**Objective:** Satisfy ADR-CORE-0031, ADR-CORE-0032, ADR-CORE-0034 minimum artifacts.

**Duration:** 1–2 maintainer days

| Task | Deliverable | Owner | Dependency |
|---|---|---|---|
| 1.1 | Create `compliance/` directory | Directory | None |
| 1.2 | Copy and complete `compliance/MODULE-COMPLIANCE.md` from OK-Core template | File | 1.1 |
| 1.3 | Create `compliance/DOCUMENTATION-REGISTRY-CONSUMPTION-LOG.md` with v1.0.0 entry | File | 1.1, registry read |
| 1.4 | Perform mandatory reading per OK-Core order | Activity | None |
| 1.5 | Create `compliance/MANDATORY-READING-CONSUMPTION-LOG.md` with actual entries | File | 1.4 |
| 1.6 | Inventory all REQUIRED/CONDITIONAL docs in MODULE-COMPLIANCE.md | Update | 1.2, registry |

**Acceptance criteria:**

- All three compliance files exist on GitHub
- Consumption log cites registry v1.0.0 and Runtime Module type
- Reading log contains date, source, version, reason, reviewer, result per entry
- MODULE-COMPLIANCE.md declares overall PASS or documents FAIL items with remediation plan

**Templates:** OK-Core `compliance/MODULE-COMPLIANCE.md`, `governance/REGISTRY-CONSUMPTION-STANDARD.md`, `governance/MANDATORY-READING-CONSUMPTION-STANDARD.md`

---

### Phase 2 — Status Document Synchronization

**Objective:** Eliminate contradictory status claims post Architecture Foundation approval.

**Duration:** 1 maintainer day

| Task | File | Change |
|---|---|---|
| 2.1 | `README.md` | Status → Architecture Foundation Approved; add governance alignment in progress |
| 2.2 | `architecture/DOD-VALIDATION.md` | Confirm Architecture Foundation PASS; reset MVP Ready to NOT SUBMITTED |
| 2.3 | `handover/OK-CORE-HANDOVER-PUBM-MVP-ARCHITECTURE-COMPLETE.md` | Remove stale "blocked until approval" for foundation gate |
| 2.4 | `governance/README.md` | Add registry v1.0.0 reference; retain legacy OK-Core ADR evidence |
| 2.5 | `roadmap/ACTIVE-WORK.md` | Replace implementation blocker with governance alignment work items |
| 2.6 | `ADRs/ADR-0001` through `ADR-0008` | Status → Accepted — aligned with OK-Core foundation approval |
| 2.7 | `CHANGELOG.md` | Record governance alignment remediation entry |

**Acceptance criteria:**

- No document claims Architecture Foundation is pending when OK-Core APR-006 exists
- DOD accurately reflects current approval state
- Local ADR statuses no longer say "pending OK-Core approval" for foundation-decision ADRs

---

### Phase 3 — Reporting Structure Creation

**Objective:** Satisfy ADR-CORE-0030 reporting requirements for formal review.

**Duration:** 1–2 maintainer days

| Task | Deliverable | Notes |
|---|---|---|
| 3.1 | Create `reviews/` directory | Required for formal reviews |
| 3.2 | Create `approval-request/RFA-PUBM-GOVERNANCE-ALIGNMENT.md` | Per OK-Core RFA template |
| 3.3 | Assign RFA-ID (OK-Core or module-proposed) | Coordinate with OK-Core |
| 3.4 | Create `reviews/evidence/<RFA-ID>/INDEX.md` | Evidence package index with GitHub paths |
| 3.5 | Create `handover/OK-CORE-HANDOVER-PUBM-GOVERNANCE-ALIGNMENT.md` | Governance alignment handover |
| 3.6 | Create `audit/PUBM-GOVERNANCE-GAP-REMEDIATION.md` | Finding record with closure criteria |

**Evidence package minimum contents:**

```text
compliance/MODULE-COMPLIANCE.md
compliance/DOCUMENTATION-REGISTRY-CONSUMPTION-LOG.md
compliance/MANDATORY-READING-CONSUMPTION-LOG.md
governance/PUBM-GOVERNANCE-GAP-ASSESSMENT.md
governance/PUBM-COMPLIANCE-MATRIX.md
governance/PUBM-APPROVAL-READINESS-REPORT.md
architecture/DOD-VALIDATION.md (updated)
handover/OK-CORE-HANDOVER-PUBM-GOVERNANCE-ALIGNMENT.md
```

**Acceptance criteria:**

- Evidence package resolves all paths on GitHub
- RFA includes registry compliance and reading compliance sections
- Handover allows OK-Core review without full repository traversal

---

### Phase 4 — govM Verification

**Objective:** Complete verification chain per `governance/GOVM-INTEGRATION-STANDARD.md`.

**Duration:** Depends on govM queue (~3–7 days)

| Task | Action |
|---|---|
| 4.1 | Commit Phases 1–3 to GitHub |
| 4.2 | Submit govM verification request referencing pubM compliance files |
| 4.3 | govM runs documentation compliance chain |
| 4.4 | govM runs reading compliance chain |
| 4.5 | Receive govM report in govM `audits/` directory |

**Target result:** PASS or CONDITIONAL PASS

**If FAIL:** Address findings in consumption logs; re-request verification (Phase 4 repeat)

---

### Phase 5 — OK-Core Governance Alignment Approval

**Objective:** Obtain formal Governance Alignment approval record.

**Duration:** Depends on OK-Core review cycle (~1–2 weeks after govM PASS)

| Task | Action |
|---|---|
| 5.1 | Submit Evidence Ready trigger to OK-Core |
| 5.2 | OK-Core evidence resolution |
| 5.3 | OK-Core review → `reviews/REVIEW-<RFA-ID>.md` |
| 5.4 | OK-Core decision: APPROVED / APPROVED WITH CONDITIONS / REJECTED |
| 5.5 | OK-Core approval record in `approvals/records/` |
| 5.6 | Closure audit SUCCESS |
| 5.7 | Lifecycle CLOSED |

**Acceptance criteria:**

- OK-Core approval record exists for Governance Alignment
- Closure audit SUCCESS documented
- pubM MODULE-COMPLIANCE.md updated with govM and OK-Core verification references

---

### Phase 6 — MVP Ready Preparation (Future)

**Objective:** Prepare for MVP Ready For Implementation RFA.

**Prerequisite:** Phase 5 complete

| Task | Action |
|---|---|
| 6.1 | Update DOD-VALIDATION.md MVP Ready section to PASS |
| 6.2 | Create MVP Ready handover |
| 6.3 | Submit RFA `Approval Type: MVP Ready` |
| 6.4 | Document deferred canonical API promotion (Issue #28) as accepted risk |

**Note:** MVP Ready does not require implementation. It authorizes implementation planning and build order entry.

---

## Task Priority Matrix

| ID | Task | Priority | Phase | Blocker For |
|---|---|---|---|---|
| R-001 | MODULE-COMPLIANCE.md | P0 | 1 | All next approvals |
| R-002 | DOCUMENTATION-REGISTRY-CONSUMPTION-LOG.md | P0 | 1 | All next approvals |
| R-003 | MANDATORY-READING-CONSUMPTION-LOG.md | P0 | 1 | All next approvals |
| R-004 | Mandatory reading execution | P0 | 1 | R-003 |
| R-005 | Status document sync | P1 | 2 | Clean evidence package |
| R-006 | RFA submission | P1 | 3 | OK-Core review |
| R-007 | Evidence package | P1 | 3 | Evidence resolution |
| R-008 | govM verification | P1 | 4 | OK-Core approval |
| R-009 | OK-Core Governance Alignment approval | P1 | 5 | MVP Ready |
| R-010 | MVP Ready RFA | P2 | 6 | Implementation authorization |

---

## Risk Register

| Risk | Severity | Mitigation |
|---|---|---|
| Reading log fabricated instead of actual reading | HIGH | Maintainers must perform reading; govM verifies |
| Compliance files created but not committed | HIGH | GitHub is system of record; commit before RFA |
| govM queue delay | MEDIUM | Submit early; track in ACTIVE-WORK |
| Stale status docs confuse reviewers | MEDIUM | Phase 2 before Phase 3 submission |
| Canonical API deferral questioned at MVP Ready | LOW | Reference Issue #28 accepted risk explicitly |
| HYBRID deployment scope creep | LOW | No architecture changes; ADR-0026 already approved |

---

## Success Metrics

| Metric | Current | Target (Phase 5) |
|---|---|---|
| Overall governance compliance | ~48% | ≥90% |
| Registry compliance | 73% | 100% |
| Reading compliance | 0% | 100% |
| Reporting compliance | 43% | ≥85% |
| govM verification | Not started | PASS |
| OK-Core Governance Alignment | Not started | APPROVED |

---

## Timeline Estimate

| Phase | Duration | Cumulative |
|---|---|---|
| Phase 1 — Compliance files | 1–2 days | 2 days |
| Phase 2 — Status sync | 1 day | 3 days |
| Phase 3 — Reporting structure | 1–2 days | 5 days |
| Phase 4 — govM verification | 3–7 days | 12 days |
| Phase 5 — OK-Core approval | 7–14 days | 26 days |

**Earliest Governance Alignment Approved: approximately 2–4 weeks from remediation start.**

---

## Dependencies

| Dependency | Module | Status |
|---|---|---|
| OK-Core registry v1.0.0 | OK-Core | Available |
| OK-Core compliance templates | OK-Core | Available |
| govM verification capacity | govM | Required — not yet requested |
| OK-Core RFA registration | OK-Core | Required at Phase 3 |
| Architecture foundation approval | OK-Core | **Complete** (APR-006) |

---

## Out of Scope Confirmation

The following are explicitly **not** part of this remediation plan:

- PHP/runtime code in `src-php/`
- Database migration implementation
- API canonical promotion to OK-Core
- Platform execution HYBRID runtime workers
- MODULE-BOUNDARIES or ownership changes
- New business entities or scope expansion

---

## Related Documents

- `governance/PUBM-GOVERNANCE-GAP-ASSESSMENT.md`
- `governance/PUBM-COMPLIANCE-MATRIX.md`
- `governance/PUBM-APPROVAL-READINESS-REPORT.md`
- OK-Core `governance/ECOSYSTEM-ADOPTION-PLAN.md` (pubM position #5)
- OK-Core `governance/APPROVAL-PROCESS.md`
- OK-Core `MANDATORY-READ-MATERIAL.md`
