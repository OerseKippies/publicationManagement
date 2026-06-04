# ADR-0005 - pubM Audit Strategy

Status: Accepted locally, pending OK-Core approval
Date: 2026-06-04

## Decision

All pubM-owned mutations must create immutable PublicationAuditRecord entries.

## Required Audit Examples

- draft created
- draft modified
- publication approved
- publication scheduled
- publication published
- publication archived
- template modified
- channel configuration changed

## Rationale

Publication history must be preserved and all lifecycle decisions must remain auditable.

## Evidence

- `architecture/AUDIT-MODEL.md`
- `schemas/MARIADB-SCHEMA-DRAFT.md`
- OK-Core MODULE-BOUNDARIES local snapshot/read model rules
