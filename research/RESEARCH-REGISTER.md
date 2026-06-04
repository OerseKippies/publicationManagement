# pubM Research Register

Status: Architecture Foundation
Date: 2026-06-04

| ID | Topic | Question | Finding | Evidence / Source | Decision | Reason | Related Document |
|---|---|---|---|---|---|---|---|
| RES-001 | Content publication patterns | Should publication be separate from advertisement? | Publication lifecycle is distinct from advertisement strategy/content ownership. | OK-Core ADR-0022, MODULE-BOUNDARIES | Adopted | Prevents adM/pubM ownership collision | ADR-0001, MODULE-SCOPE |
| RES-002 | Publishing workflow patterns | Should lifecycle transitions be explicit? | Explicit state transitions improve review, approval and audit governance. | OK-Core ADR-0022, state-model reference patterns from idM/mdM | Adopted | Prevents invalid state changes | ADR-0003, state models |
| RES-003 | Content versioning patterns | Should versions be immutable? | Immutable version snapshots preserve publication history and approval evidence. | OK-Core snapshot/audit rules in MODULE-BOUNDARIES | Adopted | Supports rollback/history without owning foreign data | ADR-0002, PERSISTENCE-MODEL |
| RES-004 | Audit approaches | Should all mutations create audit records? | All publication mutations must be append-only audited. | OK-Core audit/read-model rules, commL/idM audit references | Adopted | Required for approval evidence and traceability | ADR-0005, AUDIT-MODEL |
| RES-005 | Scheduling approaches | Can scheduling be represented without daemons? | Schedule records plus Cron-compatible evaluation fit Versio baseline. | OK-Core HOSTING-ASSESSMENT-VERSIO and DEPLOYMENT-STRATEGY | Adopted | Avoids unsupported long-running workers | ADR-0007, DEPLOYMENT |
| RES-006 | Versio deployment constraints | Can pubM foundation avoid unsupported runtimes? | Architecture can remain PHP/MariaDB/Cron compatible if direct marketplace automation is excluded. | OK-Core HOSTING-ASSESSMENT-VERSIO, DEPLOYMENT-MATRIX | Adopted with blocker | OK-Core must review HYBRID note versus narrowed hosted core | DOD-VALIDATION, DEPLOYMENT |
| RES-007 | Direct marketplace integration | Should pubM own direct integrations in MVP foundation? | Direct marketplace integrations risk unsupported runtime and boundary expansion. | User scope, OK-Core deployment constraints | Rejected | Out of MVP foundation scope | MODULE-SCOPE |
| RES-008 | Communication routing | Should pubM route directly to modules? | Direct routing violates OK-Core communication boundary. | OK-Core ADR-0021 | Rejected | commL owns mediation/routing | ADR-0008 |
