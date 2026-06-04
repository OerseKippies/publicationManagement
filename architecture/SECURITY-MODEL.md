# pubM Security Model

Status: Architecture Foundation

## Authentication Assumptions

pubM does not own authentication. Actor identity is supplied by identityManagement through communicationLayer according to OK-Core identity and access governance.

## Authorization Assumptions

pubM authorization is permission-based and must be checked before publication mutations. Permission definitions and access policy ownership remain with identityManagement.

Expected permission examples for future review:

- pubm.publication.read
- pubm.publication.draft.write
- pubm.publication.approve
- pubm.publication.schedule
- pubm.publication.publish
- pubm.template.manage
- pubm.channel.manage

## Service Account Assumptions

Service accounts are owned by identityManagement. pubM may accept service account references for approved scheduled processing after OK-Core approval.

## Audit Requirements

Every mutation must create an immutable PublicationAuditRecord with actor reference, correlation ID when available and state change details.

## Privacy Baseline

pubM follows OK-Core personal-data minimization principles:

- store references instead of full personal records
- avoid copying customer/contact/user details
- keep snapshots only when needed for audit/history
- do not make snapshots source of truth
- log personal-data access attempts when approved contracts require personal data
- require a stated purpose for any future personal-data request

Evidence:

- OK-Core issue #4, `Governance baseline: Personal Data Protection v1`
- OK-Core `architecture/MODULE-BOUNDARIES.md`

## Production Hardening Requirements

Implementation must validate:

- authentication envelope through commL
- permission checks before every mutation
- audit record creation transactionally with mutation
- sensitive data redaction in errors
- HTTPS-only API access
