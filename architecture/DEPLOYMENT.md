# pubM Deployment Model

Status: Accepted
Module: publicationManagement (pubM)
Authority: OK-Core ADR-0026

## OK-Core Classification

```text
Classification: HYBRID (Accepted)
Approval: APR-2026-06-05-012
ADR: OK-Core ADR-0026-PUBM-HYBRID-DEPLOYMENT-CLASSIFICATION.md
```

## HYBRID Scope Split

| Component | Runtime | Status |
|---|---|---|
| pubM core | VERSIO_HOSTED | Accepted — MVP foundation |
| Platform execution | HYBRID extension | Not in MVP foundation — requires separate approval |

### VERSIO_HOSTED Core (MVP Foundation)

```text
Publication lifecycle governance
Publication scheduling metadata
Publication audit records
Publication API (draft)
MariaDB 10.6 persistence
PHP 8.3 + Cron scheduling triggers
```

### HYBRID Extension (Out of MVP Foundation)

```text
PlatformExecution
PlatformFormFilling
RecorderExecution
Browser/session automation
External marketplace connectors
```

## Proven Versio Baseline (Core)

Evidence in OK-Core:

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
Node.js, npm, Docker, RabbitMQ, Redis
Python 3 runtime dependencies
WebSockets, long-running daemons
```

## Scheduling Constraint

Publication schedules are pubM-owned records. Core execution triggers use approved PHP/Cron-compatible mechanisms only.

Platform execution workers, if approved later, must communicate through communicationLayer.

## Local Execution

Local execution is not part of this MVP foundation.
