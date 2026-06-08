# DOMAIN-MODEL — pubM

## Owned entities

| Entity | Table | Owner |
|---|---|---|
| Platform | pubm_platforms | pubM |
| PlatformAccount | pubm_platform_accounts | pubM |
| PublicationRegistry | pubm_publication_registry | pubM |
| PublicationStatusHistory | pubm_publication_status_history | pubM |
| PublicationMetric | pubm_publication_metrics | pubM |
| PublicationHealth | pubm_publication_health | pubM |
| PublicationRenewal | pubm_publication_renewals | pubM |
| PublicationNote | pubm_publication_notes | pubM |

Phase 2 lifecycle tables (`publications`, drafts, versions, schedules) remain for OK-Core approval flow.

## NOT owned

Advertisement (adM), Lead/Sale (salesMP), CatalogItem (mdM), Inventory (invM).

## Registry statuses

DRAFT → PUBLISHED → STALE / EXPIRED → ARCHIVED / REMOVED

## References

`advertisementReference` stores adM UUID only — no advertisement duplication.
