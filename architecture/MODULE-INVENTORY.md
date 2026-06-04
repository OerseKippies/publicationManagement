# pubM Module Inventory

Status: Architecture Foundation
Repository: OerseKippies/publicationManagement
Module: publicationManagement
Code: pubM
Authority: OK-Core

## Purpose

pubM owns publication lifecycle governance and publication history.

## Owned Concepts

| Concept | Ownership | Notes |
|---|---|---|
| Publication | Owned | Core publication instance and lifecycle anchor |
| PublicationDraft | Owned | Draft content snapshot and review state |
| PublicationTemplate | Owned | Template used by pubM to prepare publication output |
| PublicationChannel | Owned | Channel configuration reference within pubM boundaries |
| PublicationTarget | Owned | Target reference for publication destination, without owning marketplace integration |
| PublicationSchedule | Owned | Scheduled publication intent and timing |
| PublicationVersion | Owned | Versioned publication content/history snapshot |
| PublicationStatus | Owned | Status value and lifecycle governance |
| PublicationAuditRecord | Owned | Immutable mutation history |

## Consumed But Not Owned

| Concept | Owner | Access Rule |
|---|---|---|
| Advertisement | adManagement | Through communicationLayer only |
| AdvertisementVariant | adManagement | Through communicationLayer only |
| PublicationRequest | adManagement | Through communicationLayer only |
| User, Role, Permission, AccessPolicy, ServiceAccount | identityManagement | Through communicationLayer only |
| Breed/product/canonical identifiers | masterDataManagement | Through communicationLayer only |
| Stock/reservation/order/customer/invoice data | invM/salesM/relM/finM | Through communicationLayer only |

## Excluded Concepts

pubM does not own platform execution, platform form filling, recorder execution, direct marketplace integrations, communication routing, service mediation or external API gateway behavior in this MVP foundation.

If OK-Core later approves a broader pubM execution boundary, this inventory must be updated by ADR before implementation.

## Evidence

- OK-Core `architecture/MODULE-CATALOG.md`
- OK-Core `architecture/MODULE-BOUNDARIES.md`
- OK-Core `governance/Architectural-Decision-Records/ADR-0021-COMMUNICATIONLAYER-MANDATORY-BOUNDARY.md`
- OK-Core `governance/Architectural-Decision-Records/ADR-0022-ADVERTISEMENT-VS-PUBLICATION-OWNERSHIP.md`
