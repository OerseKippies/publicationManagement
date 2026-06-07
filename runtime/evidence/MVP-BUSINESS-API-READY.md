# MVP Business API Ready — pubM

Date: 2026-06-07
Status: PASS
Module: publicationManagement (pubM)

## Versio Endpoints

| Route | Path | HTTP |
|---|---|---|
| publication-requests/list | `GET /api/v1/publications` | 200 |
| publication-requests/create | `POST /api/v1/publications` | 200 |
| publication/status | `GET /api/v1/publications/{id}` | 200 |
| publication/audit | `GET /publications/{id}/audit` | 200 |

## commL Contracts

- `pubM.publications.list.v1` — PASS (target 200)
- `pubM.health.v1` — PASS

Evidence: OK-Core `runtime-evidence/MVP-BUSINESS-API-DEPLOYMENT.md`
