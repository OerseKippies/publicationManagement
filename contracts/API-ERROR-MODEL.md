# pubM API Error Model

Status: DRAFT_IN_MODULE

## Error Shape

```json
{
  "error": {
    "code": "VALIDATION_ERROR",
    "message": "Validation failed.",
    "details": [],
    "correlationId": "optional-correlation-id"
  }
}
```

## Errors

| HTTP | Code | Use |
|---:|---|---|
| 400 | VALIDATION_ERROR | Missing or invalid fields |
| 401 | UNAUTHENTICATED | Missing authentication context |
| 403 | FORBIDDEN | Permission failure or boundary violation |
| 404 | NOT_FOUND | pubM record not found |
| 409 | CONFLICT | Invalid lifecycle transition or version conflict |
| 500 | INTERNAL_ERROR | Unexpected pubM failure |

## Boundary Violations

Requests attempting to mutate advertisements, identity records, master data, inventory, orders, communication routes or marketplace integrations must return FORBIDDEN.
