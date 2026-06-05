# pubM Runtime Evidence — Schedule Execution Walkthrough

Date: 2026-06-06

---

## Scheduling Model

- No long-running daemons
- No Redis/RabbitMQ
- Cron-compatible CLI processor
- Database state transitions

---

## Schedule Creation

`ScheduleService::schedule()` creates `publication_schedules` row:

| Field | Value |
|---|---|
| scheduleStatus | PENDING |
| scheduledAt | UTC datetime |
| maxRetries | configurable (default 3) |

Publication status: `APPROVED` → `SCHEDULED`

---

## Cron Processor

Script: `scripts/process_scheduled_publications.php`

Recommended cron (Versio hosting):

```cron
* * * * * /usr/bin/php /path/to/publicationManagement/scripts/process_scheduled_publications.php >> /var/log/pubm-scheduler.log 2>&1
```

---

## Processing Logic

1. Query `PENDING` schedules where `scheduledAt <= now()`
2. Mark schedule `PROCESSING`
3. Call `PublicationService::publish()`
4. On success: schedule → `COMPLETED`, publication → `PUBLISHED`
5. On failure: increment `retryCount`
   - If `retryCount > maxRetries`: schedule → `FAILED`
   - Else: schedule → `PENDING` (retry on next cron run)

---

## Test Evidence

Automated test in `tests/run.php` — function `run_schedule_test()`:

1. Creates and approves publication
2. Schedules with past `scheduledAt`
3. Runs `ScheduleService::processDueSchedules()`
4. Asserts publication status is `PUBLISHED`

---

## Sample Processor Output

```json
{
  "processed": 1,
  "succeeded": 1,
  "failed": 0,
  "results": [
    {
      "scheduleId": "...",
      "publicationId": "...",
      "status": "COMPLETED",
      "publicationStatus": "PUBLISHED"
    }
  ]
}
```
