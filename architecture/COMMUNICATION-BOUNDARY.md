# pubM Communication Boundary Review

Status: Architecture Foundation

## Required Rule

All cross-module communication routes through communicationLayer.

```text
Module -> communicationLayer (commL) -> Module
```

## Inbound Examples

| Source | Through | pubM Action |
|---|---|---|
| adManagement | commL | Create publication draft/request reference |
| OK-Cockpit | commL | Request publication status |
| reporting consumer | commL | Read publication lifecycle snapshot |

## Outbound Examples

| pubM Need | Through | Target |
|---|---|---|
| Validate actor reference | commL | identityManagement |
| Resolve reference data | commL | masterDataManagement |
| Notify status change | commL | subscribed modules |

## Boundary Review Result

PASS for architecture foundation. No direct module, direct foreign database, foreign internal code or shared mutable table dependency is documented.
