# ADR-LOCAL-0001 - pubM Publication Lifecycle Ownership

Status: Accepted — aligned with OK-Core Architecture Foundation approval (APR-2026-06-05-006)
Date: 2026-06-04

## Decision

publicationManagement owns publication lifecycle governance and publication history for the MVP foundation.

## Scope Clarification

This local ADR narrows the MVP foundation to the entities listed in `MODULE-SCOPE.md`:

- Publication
- PublicationDraft
- PublicationTemplate
- PublicationChannel
- PublicationTarget
- PublicationSchedule
- PublicationVersion
- PublicationStatus
- PublicationAuditRecord

## Exclusions

The MVP foundation does not include runtime execution, direct marketplace integrations, platform form filling, browser/session automation, service mediation, communication routing or external API gateway ownership.

## Rationale

OK-Core ADR-0022 separates advertisement ownership from publication ownership. adManagement may request publication actions, but pubM owns publication lifecycle and history.

The narrowed MVP scope keeps pubM compatible with the Versio baseline and avoids implementation before OK-Core approval.

## Evidence

- OK-Core `architecture/MODULE-BOUNDARIES.md`
- OK-Core `governance/Architectural-Decision-Records/ADR-0022-ADVERTISEMENT-VS-PUBLICATION-OWNERSHIP.md`
- `MODULE-SCOPE.md`
- `architecture/MODULE-INVENTORY.md`
