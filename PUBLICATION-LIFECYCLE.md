# PUBLICATION-LIFECYCLE — pubM

## Registry lifecycle (operator)

```text
DRAFT → PUBLISHED → STALE / EXPIRED → ARCHIVED / REMOVED
```

| Action | API | Result |
|---|---|---|
| Publish | POST `.../publish` | DRAFT → PUBLISHED |
| Renew | POST `.../renew` | Sets renewal date, PUBLISHED |
| Archive | POST `.../archive` | → ARCHIVED |
| Expire | POST `.../expire` | → EXPIRED |
| Remove | POST `.../remove` | → REMOVED |

All transitions recorded in `pubm_publication_status_history`.

## Phase 2 lifecycle (OK-Core)

`DRAFT → REVIEW → APPROVED → SCHEDULED → PUBLISHED` via `/publications/{id}/...` routes.

## Health engine

Detects: missing URL, renewal overdue, expired, orphan, missing advertisement, stale publication.

## Renewal engine

Summarizes: today, this week, overdue renewals.
