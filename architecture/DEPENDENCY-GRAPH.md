# pubM Dependency Graph

Status: Architecture Foundation

## Required Communication Pattern

```text
adManagement -> communicationLayer -> publicationManagement
publicationManagement -> communicationLayer -> identityManagement
publicationManagement -> communicationLayer -> masterDataManagement
publicationManagement -> communicationLayer -> adManagement
publicationManagement -> communicationLayer -> OK-Cockpit/reporting consumers
```

## Forbidden Paths

```text
publicationManagement -> adManagement database
publicationManagement -> identityManagement database
publicationManagement -> masterDataManagement database
publicationManagement -> foreign internal code
foreign module -> pubM database
```

## Dependency Notes

- pubM consumes publication requests from adManagement through commL.
- pubM consumes actor/auth context from identityManagement through commL.
- pubM consumes reference data from masterDataManagement through commL.
- pubM emits publication lifecycle/status/audit events through commL after approval.
