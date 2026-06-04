# pubM Audit Model

Status: Architecture Foundation

## Principle

All publication mutations must be auditable.

PublicationAuditRecord is immutable.

## Required Audit Coverage

| Event | Required |
|---|---:|
| draft created | Yes |
| draft modified | Yes |
| draft submitted | Yes |
| draft approved or rejected | Yes |
| publication approved | Yes |
| publication scheduled | Yes |
| schedule cancelled | Yes |
| publication published | Yes |
| publication archived | Yes |
| publication retired | Yes |
| template modified | Yes |
| channel configuration changed | Yes |
| version created | Yes |

## Audit Record Fields

- auditRecordId
- entityType
- entityId
- action
- previousState
- newState
- actorModule
- actorIdRef
- correlationId
- reason
- createdAt

## Immutability

Audit records are append-only. Corrections require a new audit record that references the earlier record.

## Privacy Baseline

Audit records store actor references and minimal necessary snapshots. Personal data must be minimized and should be retrieved from identity/relationship owners through communicationLayer when needed.

If a future approved pubM contract requests personal data, the access must be logged with requesting module, actor or service account reference when available, timestamp, target record reference, requested fields, stated purpose and access result, aligned with OK-Core issue #4.
