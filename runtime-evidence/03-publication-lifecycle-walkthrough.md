# pubM Runtime Evidence — Publication Lifecycle Walkthrough

Date: 2026-06-06

---

## Flow

```text
Create Draft (DRAFT)
  → Update Draft (DRAFT)
    → Submit Review (REVIEW)
      → Approve (APPROVED + version snapshot)
        → Publish (PUBLISHED)
```

Alternative branch after Approve:

```text
Approve (APPROVED)
  → Schedule (SCHEDULED)
    → Cron processor (PUBLISHED)
```

---

## State Transitions Validated

| Step | From | To | Audit Action |
|---|---|---|---|
| Create draft | — | DRAFT | DRAFT_CREATED |
| Update draft | DRAFT | DRAFT | DRAFT_UPDATED |
| Submit review | DRAFT | REVIEW | REVIEW_SUBMITTED |
| Approve | REVIEW | APPROVED | PUBLICATION_APPROVED |
| Schedule | APPROVED | SCHEDULED | PUBLICATION_SCHEDULED |
| Publish | APPROVED/SCHEDULED | PUBLISHED | PUBLICATION_PUBLISHED |

---

## Service Layer

| Operation | Service |
|---|---|
| Create/update draft | `DraftService` |
| Submit/approve/publish | `PublicationService` |
| Schedule | `ScheduleService` |
| History | `PublicationHistoryService` |
| Audit query | `PublicationAuditService` |

---

## Test Evidence

Automated lifecycle test in `tests/run.php` — function `run_lifecycle_test()`:

1. Creates draft
2. Updates title
3. Submits for review
4. Approves (creates version)
5. Publishes
6. Verifies history contains versions

Run:

```bash
php tests/run.php
```

Integration script:

```bash
php scripts/verify_runtime.php
```

Runs full lifecycle against configured MariaDB.

---

## Ownership Boundaries

Only pubM-owned entities mutated. `sourceModule` / `sourceObjectId` are references only — no invM data copied.
