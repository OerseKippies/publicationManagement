# API-DRAFT — pubM MVP

Base: `/api` and `/v1` (Phase 2 routes)

## Health

| Method | Path |
|---|---|
| GET | `/health`, `/v1/health` |

## Platforms

| Method | Path |
|---|---|
| GET | `/api/platforms` |

## Publications (registry)

| Method | Path |
|---|---|
| GET | `/api/publications` |
| POST | `/api/publications/from-advertisement` |
| POST | `/api/publications/{id}/publish` |
| POST | `/api/publications/{id}/archive` |
| POST | `/api/publications/{id}/expire` |
| POST | `/api/publications/{id}/remove` |
| POST | `/api/publications/{id}/renew` |
| POST | `/api/publications/{id}/metrics` |

## Renewals, health, statistics

| Method | Path |
|---|---|
| GET | `/api/renewals` |
| GET | `/api/health` |
| GET | `/api/statistics` |

## Co-Pilot

| Method | Path |
|---|---|
| GET | `/api/copilot/dashboard` |
