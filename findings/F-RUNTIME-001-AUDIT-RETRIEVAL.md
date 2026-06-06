# F-RUNTIME-001 — Audit Retrieval Incomplete

Date: 2026-06-06
Severity: MEDIUM
Status: **CLOSED**

---

## Description

`PublicationAuditRepository::findByPublication()` only queried `entityType = Publication` and `entityId = publicationId`. Draft audits (`PublicationDraft` entity) were excluded from `GET /publications/{id}/audit`.

---

## Reproduction

1. Create draft via API
2. Query `GET /publications/{id}/audit`
3. `DRAFT_CREATED` missing from results
4. `scripts/verify_runtime.php` failed: expected at least 4 audit records

---

## Impact

Audit trail API incomplete — MVP audit requirement not fully met.

---

## Remediation

Updated `findByPublication()` to include audits where:
- `entityId = publicationId`, OR
- `entityId` matches draft for publication, OR
- `entityId` matches schedule for publication

File: `src-php/Audit/PublicationAuditRepository.php`

---

## Verification

- `scripts/verify_runtime.php` — PASS
- `scripts/host_verification.php` — 6 audit records returned
- `tests/run.php` — audit trail immutability PASS

---

## Result

**CLOSED**
