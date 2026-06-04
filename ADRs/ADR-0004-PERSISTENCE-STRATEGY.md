# ADR-0004 - pubM Persistence Strategy

Status: Accepted locally, pending OK-Core approval
Date: 2026-06-04

## Decision

pubM uses a pubM-owned MariaDB schema for publication lifecycle data.

## Rules

- No shared mutable tables.
- No foreign module master tables.
- Foreign module data may be referenced by ID only.
- Immutable snapshots may be stored only for audit/history and are not source of truth.
- Retention, backup and archive rules are documented before implementation.

## Rationale

MariaDB 10.6 is part of the accepted Versio baseline. A module-owned schema aligns with OK-Core database ownership rules.

## Evidence

- OK-Core `architecture/HOSTING-ASSESSMENT-VERSIO.md`
- OK-Core `architecture/MODULE-BOUNDARIES.md`
- `architecture/PERSISTENCE-MODEL.md`
- `schemas/MARIADB-SCHEMA-DRAFT.md`
