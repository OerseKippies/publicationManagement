# pubM Runtime Evidence — Audit Trail Walkthrough

Date: 2026-06-06

---

## Audit Model

- Append-only `publication_audit_records` table
- One record per mutation
- Immutable after insert
- Module-owned (pubM) — no centralized audit repository

---

## Record Fields

| Field | Purpose |
|---|---|
| auditRecordId | Unique identifier |
| entityType | Publication, PublicationDraft, etc. |
| entityId | Entity UUID |
| action | DRAFT_CREATED, REVIEW_SUBMITTED, etc. |
| previousState | Status before transition |
| newState | Status after transition |
| actorModule | USER / SERVICE_ACCOUNT / SYSTEM |
| actorIdRef | Actor UUID |
| correlationId | Request correlation |
| reason | Human-readable reason |
| detailsJson | Additional context |
| createdAt | UTC timestamp |

---

## Mutations Audited

| Mutation | Action |
|---|---|
| Draft created | DRAFT_CREATED |
| Draft updated | DRAFT_UPDATED |
| Review submitted | REVIEW_SUBMITTED |
| Publication approved | PUBLICATION_APPROVED |
| Publication scheduled | PUBLICATION_SCHEDULED |
| Publication published | PUBLICATION_PUBLISHED |

---

## Query API

```http
GET /publications/{publicationId}/audit?limit=100
```

---

## Test Evidence

Automated test in `tests/run.php` — function `run_audit_test()`:

Verifies audit records contain `DRAFT_CREATED`, `REVIEW_SUBMITTED`, `PUBLICATION_APPROVED` for a publication lifecycle.

---

## Transaction Safety

Audit records are written inside the same database transaction as the mutation — rollback removes audit entry.

Implementation: `PublicationAuditLogger` called within `beginTransaction()` / `commit()` blocks in all services.
