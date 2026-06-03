# publicationManagement (pubM)

Status: Concept / Architecture Phase
Repository: OerseKippies/publicationManagement
Prefix: pubM
Governance: OK-Core

## Purpose

publicationManagement (pubM) owns publication lifecycle management.

It manages where, when and how advertisements are published after adManagement has created the advertisement structure.

## Owns

- Publication
- PublicationCycle
- PublicationMetric
- PublicationLineage
- ExternalPublicationId
- Renewal
- PublicationStatus

## Does Not Own

- Advertisement design (adManagement)
- Sales (salesManagement)
- Contacts (relationshipManagement)
- Inventory (inventoryManagement)

## Communication Rule

All communication must pass through communicationLayer (commL).

Direct module communication is forbidden.
