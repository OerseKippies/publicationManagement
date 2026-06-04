# Publication State Model

Status: Architecture Foundation

## States

| State | Meaning |
|---|---|
| DRAFT | Publication exists but is still being drafted |
| REVIEW | Publication is ready for review |
| APPROVED | Publication is approved for scheduling or publishing |
| SCHEDULED | Publication has an active schedule |
| PUBLISHED | Publication has been published or marked as published |
| ARCHIVED | Publication is no longer active but retained |
| RETIRED | Publication is permanently retired from active lifecycle use |

## Valid Transitions

| From | To | Governance Note |
|---|---|---|
| DRAFT | REVIEW | Draft submitted for review |
| REVIEW | DRAFT | Review requests changes |
| REVIEW | APPROVED | Review approval granted |
| APPROVED | SCHEDULED | Schedule created |
| APPROVED | PUBLISHED | Immediate publish action approved |
| SCHEDULED | APPROVED | Schedule cancelled before publishing |
| SCHEDULED | PUBLISHED | Schedule due and publish action completed |
| PUBLISHED | ARCHIVED | Published record archived |
| ARCHIVED | RETIRED | Archived record retired |

## Invalid Transitions

| From | To | Reason |
|---|---|---|
| DRAFT | PUBLISHED | Must pass review/approval |
| REVIEW | SCHEDULED | Must be approved first |
| SCHEDULED | DRAFT | Must cancel schedule back to approved, then create a new draft/version |
| ARCHIVED | PUBLISHED | Archived records require new version or approval flow |
| RETIRED | Any active state | Retired is terminal |

## Diagram

```text
DRAFT -> REVIEW -> APPROVED -> SCHEDULED -> PUBLISHED -> ARCHIVED -> RETIRED
  ^        |           |             |
  |        v           v             v
  +------ DRAFT      PUBLISHED     APPROVED
```

## Lifecycle Governance

Every valid transition creates a PublicationAuditRecord.
