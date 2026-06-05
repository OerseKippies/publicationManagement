# ADR-0007 - Scheduling Strategy Under Versio Constraints

Status: Accepted — aligned with OK-Core Architecture Foundation approval (APR-2026-06-05-006)
Date: 2026-06-04

## Decision

pubM represents schedules as PublicationSchedule records and may later evaluate due schedules with PHP/Cron-compatible processing after OK-Core approval.

## Constraints

The foundation must not depend on:

```text
Node.js
npm
Docker
RabbitMQ
Redis
Python runtime dependencies
WebSockets
Long-running daemons
```

## Rationale

Cron is part of the accepted Versio baseline. A schedule record plus Cron-compatible evaluation keeps the architecture deployable without unproven runtime services.

## Evidence

- OK-Core `architecture/HOSTING-ASSESSMENT-VERSIO.md`
- OK-Core `architecture/DEPLOYMENT-STRATEGY.md`
- `architecture/DEPLOYMENT.md`
- `architecture/state-models/PUBLICATION-SCHEDULE-STATE-MODEL.md`
