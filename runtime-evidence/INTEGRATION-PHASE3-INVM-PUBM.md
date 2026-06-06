# Phase 3 Integration Evidence — pubM (invM source)

Date: 2026-06-06  
Phase: 3  
Module: publicationManagement (pubM)

## Changes

| Component | Change |
|---|---|
| `CommLGateway.php` | Live commL route via `POST /api/route.php` + contract `mdM.masterData.validate.v1` |
| `config.integration.example.php` | `comml.enabled=true`, baseUrl `http://127.0.0.1:18080` |
| `Application.php` | Contract alias `POST /api/v1/publications` |
| `DraftService.php` | Validates `sourceObjectId` as inventoryItem via commL (unchanged logic) |

## Pass Criteria

| Criterion | Status |
|---|---|
| commL enabled in integration config | PASS |
| Live mdM validation (not UUID stub) | PASS |
| Accept publication with sourceObjectId from invM | PASS |
| Reject unvalidated sourceObjectId | PASS |
| Audit record on draft create | PASS |

## Negative Test

Publication draft with unknown `sourceObjectId` → mdM returns `valid: false` → pubM returns validation error via commL mediation.

## Positive Test

Registered inventoryItem reference → mdM valid → pubM draft created with `sourceModule=inventoryManagement` and matching `sourceObjectId`.

## VS-GAP-002

Closed — mdM live validation enforced in publication path when `comml.enabled=true`.
