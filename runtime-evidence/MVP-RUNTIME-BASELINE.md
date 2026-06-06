# pubM MVP Runtime Baseline

Date: 2026-06-06
Approval: **SUBMITTED — pending OK-Core APR-2026-06-06-023**
Commit baseline: 328eddd

Purpose: Approved runtime scope baseline. Future changes compare against this document.

---

## Approved Runtime Scope

| Component | Approved |
|---|---|
| Module | publicationManagement (pubM) |
| Phase | 2 — Publication Lifecycle Runtime |
| Hosting | VERSIO_HOSTED (MVP core) |
| Stack | PHP 8.3, MariaDB 10.6, Cron, HTTPS (when deployed) |
| Deployment class | HYBRID — core only; platform execution deferred |

---

## Approved Lifecycle

```text
DRAFT → REVIEW → APPROVED → SCHEDULED → PUBLISHED
```

| Transition | Trigger |
|---|---|
| DRAFT → REVIEW | POST /publications/{id}/submit-review |
| REVIEW → APPROVED | POST /publications/{id}/approve |
| APPROVED → SCHEDULED | POST /publications/{id}/schedule |
| APPROVED/SCHEDULED → PUBLISHED | POST /publications/{id}/publish or cron processor |

Invalid transitions rejected per architecture/state-models/PUBLICATION-STATE-MODEL.md.

---

## Approved API Scope

Entry point: `public/api/publications/index.php`

| Method | Endpoint |
|---|---|
| GET | /health |
| POST | /publications/drafts |
| GET | /publications/drafts/{id} |
| PUT | /publications/drafts/{id} |
| POST | /publications/{id}/submit-review |
| POST | /publications/{id}/approve |
| POST | /publications/{id}/schedule |
| POST | /publications/{id}/publish |
| GET | /publications/{id} |
| GET | /publications/{id}/history |
| GET | /publications/{id}/audit |

Headers: `X-Correlation-Id`, `X-Actor-Type`, `X-Actor-Id`

---

## Approved Persistence Scope

Database: `nol_module_pubm` (Versio MariaDB 10.6)

| Table | Purpose |
|---|---|
| publications | Lifecycle anchor |
| publication_drafts | Editable draft |
| publication_templates | Reusable templates |
| publication_channels | Channel configuration |
| publication_targets | Destination references |
| publication_schedules | Cron-driven scheduling |
| publication_versions | Immutable snapshots |
| publication_audit_records | Append-only audit |
| pubm_schema_migrations | Migration tracking |

Schema: `schemas/001_publications.sql`

---

## Approved Scheduling Scope

| Item | Approved mechanism |
|---|---|
| Schedule storage | publication_schedules table |
| Execution | `scripts/process_scheduled_publications.php` |
| Trigger | Versio cron (CLI, no daemon) |
| Retry | retryCount / maxRetries in ScheduleService |
| Failure | FAILED status after max retries |

No Redis, RabbitMQ, WebSockets, or long-running daemons.

---

## Approved Audit Scope

| Rule | Requirement |
|---|---|
| Storage | publication_audit_records |
| Mutability | Append-only — no updates/deletes |
| Coverage | Every mutation logged |
| Retrieval | GET /publications/{id}/audit |
| Ownership | Module-owned — no centralized audit repository |

Actions: DRAFT_CREATED, DRAFT_UPDATED, REVIEW_SUBMITTED, PUBLICATION_APPROVED, PUBLICATION_SCHEDULED, PUBLICATION_PUBLISHED

---

## Approved Ownership (No Expansion)

pubM owns ONLY:

```text
Publication, PublicationDraft, PublicationTemplate, PublicationChannel,
PublicationTarget, PublicationSchedule, PublicationVersion, PublicationStatus,
PublicationAuditRecord
```

Cross-module access: commL only (CommLGateway stub in MVP standalone mode).

---

## Out of Approved Baseline

| Item | Status |
|---|---|
| HYBRID platform execution | Deferred |
| Canonical API promotion | Issue #28 |
| Direct invM/adM coupling | Phase 3+ via commL |
| Versio HTTPS deploy | F-DEPLOY-001 OPEN |

---

## Verification Baseline

| Script | Result at approval |
|---|---|
| scripts/migrate.php | PASS |
| scripts/verify_runtime.php | PASS |
| scripts/host_verification.php | PASS |
| tests/run.php | 6/6 PASS |

Evidence: runtime-evidence/ host verification package @328eddd

---

## Change Control

Changes to approved baseline require:

- Architecture impact assessment if boundaries affected
- OK-Core review if API contract or ownership changes
- Updated runtime evidence for MVP scope changes

Compare future runtime diffs against commit `328eddd` and this document.
