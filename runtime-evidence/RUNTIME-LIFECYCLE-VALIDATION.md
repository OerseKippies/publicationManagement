# pubM Runtime Lifecycle Validation

Date: 2026-06-06
Script: `scripts/host_verification.php`
Publication ID: `a05c5df1-c771-4de5-9ad1-ec85b03977e6`

---

## Lifecycle Executed

```text
Create Draft (DRAFT)
  → Update Draft (DRAFT)
    → Submit Review (REVIEW)
      → Approve (APPROVED)
        → Schedule (SCHEDULED)
          → Cron Process (PUBLISHED)
```

---

## Step Results

| Step | Expected Status | Actual Status | Result |
|---|---|---|---|
| Create draft | DRAFT | DRAFT | PASS |
| Update draft | DRAFT | DRAFT (title updated) | PASS |
| Submit review | REVIEW | REVIEW | PASS |
| Approve | APPROVED | APPROVED | PASS |
| Schedule | SCHEDULED | SCHEDULED | PASS |
| Cron publish | PUBLISHED | PUBLISHED | PASS |

---

## Summary Output

```json
{
  "publicationId": "a05c5df1-c771-4de5-9ad1-ec85b03977e6",
  "finalStatus": "PUBLISHED",
  "auditRecordCount": 6,
  "allTablesPresent": true,
  "pass": true
}
```

Full capture: `runtime-evidence/host-verification-output.json`

---

## Additional CLI Verification

```text
D:\Programs\PHP\php.exe scripts/verify_runtime.php
```

Output:

```text
PASS: create draft
PASS: submit review
PASS: approve publication
PASS: publish publication
PASS: audit trail present
Runtime verification PASSED
```

---

## Result

**PASS** — full publication lifecycle validated on Versio MariaDB.
