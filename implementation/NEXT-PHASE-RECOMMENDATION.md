# Next Phase Recommendation — invM → pubM Integration

Date: 2026-06-06
Module: publicationManagement (pubM)
Authority: OK-Core/implementation/IMPLEMENTATION-BUILD-ORDER.md Phase 3

Prior phase: Phase 2 pubM MVP Runtime — **SUBMITTED @328eddd** (pending OK-Core APR-2026-06-06-023)

**Do not start implementation in this document.** Recommendation only.

---

## Recommended Next Phase

**Phase 3 — Integration (invM → pubM)**

Inventory item creation in invM flows to publication request in pubM via commL.

---

## Objectives

1. Connect invM inventory items to pubM publication drafts via commL contract route
2. Enforce mdM master data validation before publication acceptance (via commL)
3. Prove inventory → publication flow with integration tests
4. Capture runtime evidence for Phase 3 checkpoint
5. Enable idM actor context through commL on cross-module calls

---

## Dependencies

### Satisfied

| Dependency | Status |
|---|---|
| invM MVP Runtime | APPROVED WITH CONDITIONS (APR-2026-06-06-022) |
| pubM MVP Runtime | SUBMITTED @328eddd | reviews/RFA-PUBM-MVP-RUNTIME.md |
| commL MVP Runtime | APPROVED (APR-2026-06-06-021) |
| idM MVP Runtime | APPROVED (APR-2026-06-05-017) |
| mdM MVP Runtime | APPROVED (APR-2026-06-05-019) |

### Required for Phase 3

| Dependency | Action |
|---|---|
| commL route registration | invM → pubM publication request route |
| commL mdM validation route | Enable `comml.enabled = true` in pubM config |
| invM inventory API | Consume via commL — do not direct HTTP |

---

## Required Evidence

| Evidence | Location |
|---|---|
| Integration tests | tests/ or scripts/ integration suite |
| commL contract route | contracts/runtime/ or commL registry reference |
| Runtime evidence | runtime-evidence/ Phase 3 capture |
| Checkpoint review | reviews/REVIEW-INTEGRATION-CHECKPOINT-PHASE3.md |
| mdM validation proof | Publication rejected without valid reference |

Per MVP-ACCELERATION-PROGRAM.md Phase 3 deliverables.

---

## Expected Deliverables

| Deliverable | Required |
|---|---|
| Integration tests (invM → pubM) | YES |
| Runtime evidence | YES |
| commL contract route | YES |
| Phase 3 checkpoint review | YES |

**Not in Phase 3 scope:**

- adM integration (Phase 4)
- End-to-end vertical slice (Phase 5)
- F-DEPLOY-001 closure (parallel track — Versio HTTPS deploy)
- HYBRID platform execution

---

## Suggested Flow

```text
Create InventoryItem (invM)
  → Master Data Validation (mdM via commL)
    → Create Publication Draft (pubM via commL)
      → [Phase 4: Advertisement (adM)]
```

---

## Preconditions

| Precondition | Status |
|---|---|
| pubM Phase 2 lifecycle operational | Satisfied |
| invM inventory API operational | Satisfied |
| commL routing available | Satisfied |
| No direct module coupling | Architecture constraint |

---

## Checkpoint

Create on Phase 3 completion:

```text
reviews/REVIEW-INTEGRATION-CHECKPOINT-PHASE3.md
```

Template: OK-Core/implementation/CHECKPOINT-REVIEW-TEMPLATE.md

---

## Reference

- OK-Core/implementation/MVP-ACCELERATION-PROGRAM.md — Phase 3
- OK-Core/implementation/FIRST-VERTICAL-SLICE-DECISION.md
- publicationManagement/contracts/runtime/COMML-BOUNDARY.md
- publicationManagement/runtime-evidence/MVP-RUNTIME-BASELINE.md
