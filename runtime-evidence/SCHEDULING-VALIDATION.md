# pubM Scheduling Validation

Date: 2026-06-06
Script: `scripts/process_scheduled_publications.php` (via host_verification.php)

---

## Schedule Creation

Request:

```json
{
  "scheduledAt": "2026-06-06 00:00:46",
  "maxRetries": 3
}
```

Result: publication status `APPROVED` → `SCHEDULED`, schedule row `PENDING`.

---

## Cron Processor Execution

Command:

```text
D:\Programs\PHP\php.exe scripts/process_scheduled_publications.php
```

Host verification cron output:

```json
{
  "processed": 1,
  "succeeded": 1,
  "failed": 0,
  "results": [
    {
      "scheduleId": "...",
      "publicationId": "a05c5df1-c771-4de5-9ad1-ec85b03977e6",
      "status": "COMPLETED",
      "publicationStatus": "PUBLISHED"
    }
  ]
}
```

---

## Validations

| Check | Expected | Actual | Result |
|---|---|---|---|
| Cron-compatible CLI script | No daemon required | `process_scheduled_publications.php` | PASS |
| Due schedule detection | PENDING + scheduledAt <= now | Processed 1 | PASS |
| Publication publish on due | PUBLISHED | PUBLISHED | PASS |
| Schedule status after success | COMPLETED | COMPLETED | PASS |
| Retry strategy present | retryCount / maxRetries | Implemented in ScheduleService | PASS |
| Audit on schedule/publish | PUBLICATION_SCHEDULED, PUBLICATION_PUBLISHED | Present in audit trail | PASS |

---

## Retry Handling (Code Verified)

On failure: increment `retryCount`, revert to `PENDING` if under `maxRetries`, else `FAILED`.

Test evidence: `tests/run.php` — `schedule cron processing` PASS on MariaDB.

---

## Versio Cron

Cron job not yet registered on Versio control panel. Script is cron-compatible per ADR-0007.

---

## Result

**PASS** — scheduling and cron execution validated on target MariaDB stack.
