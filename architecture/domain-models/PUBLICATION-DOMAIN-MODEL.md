# pubM Publication Domain Model

Status: Architecture Foundation

## Entity Summary

| Entity | Purpose | Key Relationships |
|---|---|---|
| Publication | Lifecycle anchor for a publication instance | Has drafts, versions, schedules, status and audit records |
| PublicationDraft | Editable draft before approval | Belongs to Publication |
| PublicationTemplate | Reusable pubM template | Referenced by draft/publication |
| PublicationChannel | Channel configuration record | Referenced by PublicationTarget |
| PublicationTarget | Destination reference for a publication | Belongs to Publication and PublicationChannel |
| PublicationSchedule | Scheduled publication intent | Belongs to Publication |
| PublicationVersion | Immutable publication version snapshot | Belongs to Publication |
| PublicationStatus | Current status and reason | Belongs to Publication |
| PublicationAuditRecord | Immutable mutation event | References pubM-owned object and actor reference |

## Publication

Represents a pubM-owned publication lifecycle instance.

Minimum attributes:

- publicationId
- sourceModule
- sourceObjectId
- currentStatus
- activeVersionId
- createdAt
- updatedAt

Foreign references are not ownership. `sourceModule` and `sourceObjectId` may refer to adManagement objects received through communicationLayer.

## PublicationDraft

Represents a mutable draft before approval.

Minimum attributes:

- draftId
- publicationId
- draftState
- titleSnapshot
- contentSnapshot
- templateId
- createdByActorRef
- updatedByActorRef

## PublicationTemplate

Represents a pubM-owned template for preparing publication output. It does not own advertisement business rules.

## PublicationChannel

Represents channel configuration that pubM can reference when preparing publication lifecycle actions. It does not own communication routing or marketplace integration.

## PublicationTarget

Represents where a publication is intended to appear. It stores target references and channel references, not external integration ownership.

## PublicationSchedule

Represents scheduled intent and timing. It does not imply a long-running worker.

## PublicationVersion

Represents immutable publication content/history snapshots.

## PublicationStatus

Represents lifecycle state and status reason.

## PublicationAuditRecord

Represents immutable evidence of pubM-owned mutations. Audit records must not be updated after creation.
