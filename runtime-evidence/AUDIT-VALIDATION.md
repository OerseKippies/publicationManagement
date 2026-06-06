# pubM Audit Validation

Date: 2026-06-06
Table: publication_audit_records

---

## Immutability

| Check | Result |
|---|---|
| Append-only inserts | PASS — no update/delete methods |
| Written in transaction with mutation | PASS — services use beginTransaction/commit |
| Audit record fields populated | PASS |

---

## Completeness (Host Verification Publication)

| Action | Present |
|---|---|
| DRAFT_CREATED | Yes |
| DRAFT_UPDATED | Yes |
| REVIEW_SUBMITTED | Yes |
| PUBLICATION_APPROVED | Yes |
| PUBLICATION_SCHEDULED | Yes |
| PUBLICATION_PUBLISHED | Yes |

Total audit records for publication: **6**

---

## Audit Retrieval

Endpoint: `GET /publications/{id}/audit`

Fix applied during verification: `PublicationAuditRepository::findByPublication()` now includes draft and schedule entity audits linked to publication.

| Check | Expected | Actual | Result |
|---|---|---|---|
| Draft audits included | Yes | DRAFT_CREATED visible | PASS |
| Publication audits included | Yes | All lifecycle actions | PASS |
| Correlation ID present | Yes | On all records | PASS |

---

## Defect Resolved

| ID | Description | Status |
|---|---|---|
| F-RUNTIME-001 | Audit query missed draft records | **CLOSED** — findByPublication expanded |

See: `findings/F-RUNTIME-001-AUDIT-RETRIEVAL.md`

---

## verify_runtime.php

```text
PASS: audit trail present
```

---

## Result

**PASS** — immutable, complete audit trail with working retrieval.
