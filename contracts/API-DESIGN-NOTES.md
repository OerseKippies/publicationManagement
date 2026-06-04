# pubM API Design Notes

Status: DRAFT_IN_MODULE

## Purpose

The pubM API draft describes publication lifecycle operations before OK-Core canonical review.

## Design Rules

- The API is a draft and not implementation approval.
- All calls are mediated through communicationLayer.
- pubM endpoints expose pubM-owned publication lifecycle behavior only.
- Foreign module records are referenced by IDs and source module names.
- State-changing operations create audit records.

## Draft Use Cases

- Create publication draft from an approved publication request reference.
- Submit draft for review.
- Approve publication.
- Schedule publication.
- Mark publication published.
- Archive publication.
- Read publication status/history.

## Out Of Scope

- Creating advertisements
- Pricing advertisements
- Direct marketplace publishing
- Communication routing
- Identity management
- Master data management
