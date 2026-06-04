# PublicationDraft State Model

Status: Architecture Foundation

## States

| State | Meaning |
|---|---|
| OPEN | Draft can be edited |
| SUBMITTED | Draft has been submitted for review |
| APPROVED | Draft has been approved into a publication version |
| REJECTED | Draft was rejected and needs revision |
| SUPERSEDED | Draft was replaced by a newer draft |

## Valid Transitions

| From | To | Note |
|---|---|---|
| OPEN | SUBMITTED | Draft submitted |
| SUBMITTED | APPROVED | Review accepted |
| SUBMITTED | REJECTED | Review rejected |
| REJECTED | OPEN | Draft reopened for edits |
| OPEN | SUPERSEDED | Newer draft replaces it |
| REJECTED | SUPERSEDED | Newer draft replaces it |

## Invalid Transitions

| From | To | Reason |
|---|---|---|
| OPEN | APPROVED | Must be submitted first |
| APPROVED | OPEN | Approved draft is immutable as version source |
| SUPERSEDED | APPROVED | Superseded draft cannot become active |

## Diagram

```text
OPEN -> SUBMITTED -> APPROVED
  ^        |
  |        v
REJECTED <-+

OPEN -> SUPERSEDED
REJECTED -> SUPERSEDED
```
