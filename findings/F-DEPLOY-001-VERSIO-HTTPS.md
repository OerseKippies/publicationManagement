# F-DEPLOY-001 — Versio HTTPS Not Deployed

Date: 2026-06-06
Severity: LOW
Status: **OPEN** — canonical tracking at findings/F-DEPLOY-001.md

See: findings/F-DEPLOY-001.md (do not close)

pubM runtime code not deployed to Versio HTTPS entrypoints. Probed URLs return 404 or DNS failure.

---

## Reproduction

```text
GET https://oerse-kippies.nl/public/api/health.php → 404
GET https://pubm.oerse-kippies.nl/ → DNS not resolved
```

---

## Impact

Live HTTPS API not available on Versio. Runtime validated via CLI against Versio MariaDB only.

---

## Remediation

1. Git deploy repository to Versio host
2. Configure `public/api/publications/index.php` URL
3. Register cron for `scripts/process_scheduled_publications.php`
4. Re-capture HTTPS endpoint evidence

---

## Precedent

invM MVP Runtime approved WITH CONDITIONS (APR-2026-06-06-022) with similar Versio HTTP gap.

---

## Result

**OPEN** — recommended before OK-Core approval; not blocking RFA submission.
