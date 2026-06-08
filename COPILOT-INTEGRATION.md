# COPILOT-INTEGRATION — pubM

Module: publicationManagement (pubM)  
Production API: `https://pubm.oerse-kippies.nl`

## Boundary

pubM owns **Publication** lifecycle. copM consumes pubM via commL. pubM does not own Advertisements (adM), Leads, or Sales.

## Co-Pilot dashboard

```
GET /api/copilot/dashboard
```

### Response fields

| Field | Description |
|---|---|
| publication_count | Total registry entries |
| published_count | Status PUBLISHED |
| stale_count | Status STALE |
| expired_count | Status EXPIRED |
| renewal_due_count | Renewals due within 7 days |
| health_issue_count | Open health findings |

### Widgets

Published, Expiring Soon, Expired, Needs Renewal, Health Issues, Top Publications, Platform Distribution.

## Create from advertisement (adM reference)

```
POST /api/publications/from-advertisement
```

Body: `advertisementReference` (UUID), `platformId`, optional `publicationUrl`, `renewalDate`.

One advertisement → multiple publications supported.

## Commercial flow

```text
CatalogItem (mdM) → Advertisement (adM) → Publication (pubM) → Lead (salesMP) → Sale
```
