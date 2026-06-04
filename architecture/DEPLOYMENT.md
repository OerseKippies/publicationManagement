# pubM Deployment Model

Status: Architecture Foundation
Module: publicationManagement (pubM)
Authority: OK-Core

## Target Classification

This MVP foundation is designed to be compatible with:

```text
VERSIO_HOSTED
```

OK-Core `architecture/DEPLOYMENT-MATRIX.md` currently lists pubM as a HYBRID draft candidate because platform automation and browser/session tooling are not proven on the Versio baseline. This local foundation deliberately excludes direct marketplace integrations, browser automation, WebSockets, long-running daemons and external API gateway ownership.

Therefore this foundation requests OK-Core review of the scoped VERSIO_HOSTED pubM core:

```text
publication lifecycle + scheduling metadata + audit + API draft
```

## Proven Versio Baseline

Evidence is in OK-Core:

- `architecture/HOSTING-ASSESSMENT-VERSIO.md`
- `architecture/DEPLOYMENT-STRATEGY.md`
- `inventories/00-Hosting-Versio/`

Supported baseline:

```text
PHP 8.3
MariaDB 10.6
HTTPS APIs
Cron
SSH
Git deployment
Filesystem storage where appropriate
```

Not supported as baseline:

```text
Node.js
npm
Docker
RabbitMQ
Redis
Python 3 runtime dependencies
WebSockets
Long-running daemons
```

## Scheduling Constraint

Publication schedules are represented as pubM-owned records. Execution may only be triggered by approved PHP/Cron-compatible mechanisms after OK-Core approval.

No architecture in this foundation requires a persistent worker, browser session, marketplace connector or direct external API gateway.

## Local Execution

Local execution is not part of this MVP foundation. If future publication execution requires browser/session automation, that worker must be separately approved and must still communicate through communicationLayer.
