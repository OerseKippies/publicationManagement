# OK-Core Handover — pubM MVP Runtime Approval

Date: 2026-06-06  
Module: publicationManagement (pubM)  
Repository: OerseKippies/publicationManagement  
Commit: `328eddd`  
Authority: OK-Core START-HERE.md, `governance/APPROVAL-PROCESS.md`

## Purpose

Submit pubM for OK-Core MVP Runtime Approval via RFA-PUBM-MVP-RUNTIME.

Architecture Foundation: **APPROVED** (APR-2026-06-05-006).  
Deployment Classification: **APPROVED** (APR-2026-06-05-012).  
Phase 2 Runtime Build: **COMPLETE**.  
Module Review Decision: **APPROVED WITH CONDITIONS**.

## Runtime Overview

Working publication lifecycle runtime on PHP 8.3 + MariaDB (Versio shared hosting target).

| Layer | Location |
|---|---|
| HTTP API | `public/api/publications/index.php`, `public/api/health.php` |
| Application | `src-php/Application.php` |
| Domain services | `src-php/Domain/Service/` |
| Persistence | `schemas/001_publications.sql` |
| Audit | `src-php/Audit/PublicationAuditRepository.php` (append-only) |
| Scheduling | `scripts/process_scheduled_publications.php` |

## Implemented APIs (Phase 2)

| Method | Path |
|---|---|
| GET | `/health` |
| POST | `/publications/drafts` |
| GET | `/publications/drafts/{id}` |
| PUT | `/publications/drafts/{id}` |
| POST | `/publications/{id}/submit-review` |
| POST | `/publications/{id}/approve` |
| POST | `/publications/{id}/schedule` |
| POST | `/publications/{id}/publish` |
| GET | `/publications/{id}` |
| GET | `/publications/{id}/history` |
| GET | `/publications/{id}/audit` |

## Persistence

Database: `nol_module_pubm` (Versio MariaDB 10.6)

| Table | Purpose |
|---|---|
| publications | Lifecycle anchor |
| publication_drafts | Editable draft |
| publication_templates | Templates |
| publication_channels | Channels |
| publication_targets | Targets |
| publication_schedules | Cron scheduling |
| publication_versions | Immutable snapshots |
| publication_audit_records | Append-only audit |

Apply schema: `php scripts/migrate.php`

## Audit Implementation

Actions logged: DRAFT_CREATED, DRAFT_UPDATED, REVIEW_SUBMITTED, PUBLICATION_APPROVED, PUBLICATION_SCHEDULED, PUBLICATION_PUBLISHED

- Insert-only audit repository
- Actor context via `X-Actor-Type`, `X-Actor-Id`
- Correlation via `X-Correlation-Id`

## Verification

```text
php scripts/migrate.php
php scripts/verify_runtime.php        — PASS
php scripts/host_verification.php     — PASS @328eddd
php tests/run.php                     — 6/6 PASS
```

## Deployment Compliance

| Check | Result |
|---|---|
| PHP 8.3 | PASS |
| MariaDB 10.6 | PASS |
| Cron (no daemon) | PASS |
| VERSIO_HOSTED core | PASS |
| HYBRID extension | Deferred |
| Versio HTTPS deploy | CONDITIONAL — F-DEPLOY-001 |

## Submission Package

| Document | Path |
|---|---|
| RFA | `reviews/RFA-PUBM-MVP-RUNTIME.md` |
| Module runtime review | `reviews/RUNTIME-APPROVAL-REVIEW.md` |
| Findings | `reviews/RUNTIME-APPROVAL-FINDINGS.md` |
| Checkpoint | `reviews/REVIEW-pubm-CHECKPOINT-PHASE2.md` |
| Runtime evidence | `runtime-evidence/` |
| DoD | `architecture/DOD-VALIDATION.md` |
| Gap analysis | `audit/PUBM-MVP-RUNTIME-GAP-ANALYSIS.md` |
| Closure audit (module) | `audits/PUBM-MVP-RUNTIME-CLOSURE-AUDIT.md` |

## Known Limitations

1. Versio HTTPS not deployed — F-DEPLOY-001 OPEN
2. commL live integration deferred — Phase 3
3. Canonical API promotion deferred — Issue #28

## Requested OK-Core Action

Register MVP Runtime Complete decision:

```text
OerseKippies/OK-Core/approvals/records/APPROVAL-PUBM-MVP-RUNTIME.md
OerseKippies/OK-Core/approvals/records/INDEX.md (APR-2026-06-06-023 suggested)
OerseKippies/OK-Core/reviews/RFA-PUBM-MVP-RUNTIME-REVIEW.md
OerseKippies/OK-Core/reviews/CLOSURE-AUDIT-PUBM-MVP-RUNTIME.md
```

## Handover Conclusion

pubM Phase 2 runtime evidence package is complete at commit `328eddd` and ready for OK-Core MVP Runtime Approval.
