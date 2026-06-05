# pubM commL Runtime Boundary

Date: 2026-06-06
Module: publicationManagement (pubM)
Status: MVP Phase 2 Runtime

---

## Rule

All cross-module communication routes through **communicationLayer (commL)**.

```text
Module → commL → Module
```

pubM does **not** call invM, mdM, idM, or adM directly.

---

## Runtime Implementation

| Boundary | Implementation | Path |
|---|---|---|
| Master data validation (before publication acceptance) | `CommLGateway::validateMasterDataReference()` | `src-php/Infrastructure/CommLGateway.php` |
| Actor context | HTTP headers `X-Actor-Type`, `X-Actor-Id` (idM context via commL in integration phases) | `src-php/Infrastructure/ActorContext.php` |
| Correlation | `X-Correlation-Id` header | `src-php/Infrastructure/Correlation.php` |

---

## commL Configuration

```php
// config/config.php
'comml' => [
    'enabled' => false,  // MVP local: UUID validation stub
    'base_url' => '',    // Production: commL base URL
    'timeout_seconds' => 5,
],
```

When `comml.enabled` is `true`, pubM POSTs to:

```text
{comml.base_url}/api/comml/route/mdM/validate-reference
```

When disabled (MVP local / test), pubM validates UUID format only — no direct mdM coupling.

---

## Outbound (Future Integration Phases)

| pubM Need | Route | Target |
|---|---|---|
| Validate reference data | commL contract route | mdM |
| Resolve actor permissions | commL contract route | idM |
| Publication status notification | commL event route | subscribed modules |

Phase 3 (invM → pubM integration) will register commL contract routes. Phase 2 runtime exposes HTTP API only.

---

## Inbound (Future Integration Phases)

| Source | Through | pubM Action |
|---|---|---|
| invM | commL | Create publication draft with sourceObjectId |
| OK-Cockpit | commL | Read publication status |

Direct module HTTP calls to pubM internal endpoints are **not** permitted in MVP integration — commL routes only.

---

## Evidence

No direct imports or HTTP calls to foreign module repositories exist in `src-php/`.
