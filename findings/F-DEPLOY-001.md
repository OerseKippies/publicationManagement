# F-DEPLOY-001 — Versio HTTPS Not Deployed

Finding ID: F-DEPLOY-001
Date opened: 2026-06-06
Severity: **LOW**
Status: **OPEN**

Related submission: reviews/RFA-PUBM-MVP-RUNTIME.md (pending OK-Core APR-2026-06-06-023)

---

## Description

pubM runtime code is not deployed to Versio HTTPS entrypoints. API layer validated in-process and via CLI against Versio MariaDB; live HTTPS endpoints not available on probed host URLs.

---

## Severity

**LOW** — Does not block MVP Runtime approval. Recorded as approval condition per invM precedent (APR-2026-06-06-022).

---

## Impact

| Area | Impact |
|---|---|
| Live HTTPS API | Not available on Versio host |
| MVP vertical slice integration | Phase 3 may proceed via commL; production HTTP access deferred |
| Cron on Versio | Not registered on host control panel |

Runtime operational on Versio MariaDB via CLI. No data integrity or lifecycle impact.

---

## Reproduction

```text
GET https://oerse-kippies.nl/public/api/health.php → 404 Not Found
GET https://oerse-kippies.nl/public/api/publications/ → 404 Not Found
GET https://pubm.oerse-kippies.nl/ → DNS not resolved
```

Evidence: runtime-evidence/DEPLOYMENT-VERIFICATION.md

---

## Required Closure Evidence

To close this finding:

1. Git deploy `OerseKippies/publicationManagement` to Versio host
2. Configure web root for `public/api/publications/index.php` and `public/api/health.php`
3. Capture HTTPS health check response (200, database ok)
4. Capture at least one HTTPS API lifecycle call (create draft)
5. Register Versio cron for `scripts/process_scheduled_publications.php`
6. Add evidence to `runtime-evidence/` (hosted HTTP capture)
7. Update this finding status to CLOSED with evidence references

---

## Current Status

**OPEN** — Do not close until Versio HTTPS deployment evidence captured.

---

## Tracking

| Field | Value |
|---|---|
| Owner | pubM |
| Opened | 2026-06-06 (host verification) |
| Approval condition | Recommended — pending OK-Core decision |
| Blocks Phase 3 | Yes — until OK-Core registers pubM MVP Runtime |
| Blocks MVP Runtime submission | No |

---

## Related

- findings/F-DEPLOY-001-VERSIO-HTTPS.md (initial host verification record)
- runtime-evidence/DEPLOYMENT-VERIFICATION.md
- approvals/records/APPROVAL-PUBM-MVP-RUNTIME.md §Conditions
