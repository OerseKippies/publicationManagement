# RFA — pubM MVP Runtime Complete

Status: **READY FOR SUBMISSION**
Authority: publicationManagement (pubM)
RFA-ID: RFA-PUBM-MVP-RUNTIME-001

---

## Requested Decision

```text
publicationManagement (pubM) — MVP Runtime Complete (Phase 2)
```

Per OK-Core START-HERE.md §8 and MVP-ACCELERATION-PROGRAM.md Phase 2.

---

## Module

| Field | Value |
|---|---|
| Module | publicationManagement |
| Code | pubM |
| Repository | OerseKippies/publicationManagement |
| Phase | 2 — Publication Lifecycle Runtime |

---

## Prior Approvals

| Approval | Record |
|---|---|
| Architecture Foundation | APR-2026-06-05-006 |
| Deployment Classification (HYBRID) | APR-2026-06-05-012 |

---

## Runtime Evidence Index

| Evidence | Path |
|---|---|
| Deployment verification | runtime-evidence/DEPLOYMENT-VERIFICATION.md |
| Database validation | runtime-evidence/DATABASE-VALIDATION.md |
| Lifecycle validation | runtime-evidence/RUNTIME-LIFECYCLE-VALIDATION.md |
| API validation | runtime-evidence/API-VALIDATION.md |
| Scheduling validation | runtime-evidence/SCHEDULING-VALIDATION.md |
| Audit validation | runtime-evidence/AUDIT-VALIDATION.md |
| Test execution | runtime-evidence/TEST-EXECUTION-REPORT.md |
| Host verification output | runtime-evidence/host-verification-output.json |
| Schema validation output | runtime-evidence/schema-validation-output.json |
| Phase 2 checkpoint | reviews/REVIEW-pubm-CHECKPOINT-PHASE2.md |
| DoD validation | architecture/DOD-VALIDATION.md |

---

## Verification Summary

| Area | Result |
|---|---|
| PHP 8.3.31 | PASS |
| MariaDB 10.6 (nol_module_pubm) | PASS |
| Migration | PASS |
| Full lifecycle Draft→Published | PASS |
| Scheduling + cron processor | PASS |
| Audit (6 actions per lifecycle) | PASS |
| Tests 6/6 | PASS |
| verify_runtime.php | PASS |
| host_verification.php | PASS |

---

## Defect Status

| ID | Severity | Status |
|---|---|---|
| F-RUNTIME-001 | MEDIUM | CLOSED |
| F-DEPLOY-001 | LOW | OPEN — Versio HTTPS deploy recommended |

---

## DoD Result

```text
PASS WITH CONDITIONS
```

See architecture/DOD-VALIDATION.md

---

## Conditions (Recommended)

1. Deploy `public/api/*` to Versio HTTPS (F-DEPLOY-001)
2. Register Versio cron for `scripts/process_scheduled_publications.php`

Precedent: invM APR-2026-06-06-022 APPROVED WITH CONDITIONS.

---

## Scope Compliance

pubM owns only Publication lifecycle entities. No ownership expansion. commL stub mode for MVP standalone runtime.

---

## Request

Approve pubM MVP Runtime Complete to unblock Phase 3 integration (invM → pubM) per IMPLEMENTATION-BUILD-ORDER.md.

OK-Core decides. This RFA records pubM readiness only.
