# PublicationSchedule State Model

Status: Architecture Foundation

## States

| State | Meaning |
|---|---|
| PLANNED | Schedule exists but is not active |
| ACTIVE | Schedule is active and may be evaluated by approved Cron-compatible processing |
| DUE | Schedule time has arrived |
| COMPLETED | Scheduled action has been completed |
| CANCELLED | Schedule was cancelled |
| FAILED | Schedule evaluation failed and needs review |

## Valid Transitions

| From | To | Note |
|---|---|---|
| PLANNED | ACTIVE | Schedule activated |
| ACTIVE | DUE | Due time reached |
| DUE | COMPLETED | Publication action completed |
| PLANNED | CANCELLED | Cancel before activation |
| ACTIVE | CANCELLED | Cancel active schedule |
| DUE | FAILED | Due action failed |
| FAILED | ACTIVE | Retry approved |
| FAILED | CANCELLED | Failure closed without retry |

## Invalid Transitions

| From | To | Reason |
|---|---|---|
| PLANNED | COMPLETED | Must become due first |
| CANCELLED | ACTIVE | Cancelled schedule cannot be reactivated |
| COMPLETED | ACTIVE | Completed is terminal |
| FAILED | COMPLETED | Must retry or record remediation first |

## Diagram

```text
PLANNED -> ACTIVE -> DUE -> COMPLETED
   |         |        |
   v         v        v
CANCELLED  CANCELLED FAILED -> ACTIVE
                         |
                         v
                      CANCELLED
```
