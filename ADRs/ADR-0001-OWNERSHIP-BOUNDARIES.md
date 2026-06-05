# ADR-0001 - pubM Ownership Boundaries

Status: Accepted — aligned with OK-Core Architecture Foundation approval (APR-2026-06-05-006)
Date: 2026-06-04

## Decision

pubM owns only publication lifecycle concepts listed in `MODULE-SCOPE.md`.

## Owned

Publication, PublicationDraft, PublicationTemplate, PublicationChannel, PublicationTarget, PublicationSchedule, PublicationVersion, PublicationStatus and PublicationAuditRecord.

## Not Owned

User, role, permission, access policy, service account, breed definitions, product definitions, canonical identifiers, stock, inventory movements, reservations, orders, customers, invoices, advertisements, advertisement pricing, advertisement business rules, communication routing, service mediation, communication contracts, direct marketplace integrations and external API gateway ownership.

## Rationale

OK-Core separates advertisement ownership from publication ownership and requires one source of ownership. pubM must not become a hidden owner of foreign concepts by copying or mutating them.

## Evidence

- OK-Core `architecture/MODULE-BOUNDARIES.md`
- OK-Core ADR-0021
- OK-Core ADR-0022
- `MODULE-SCOPE.md`
