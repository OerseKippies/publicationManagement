# PM-1 Publication Evidence

Program: PM-1 Real Inventory Flow
Date: 2026-06-07
Module: publicationManagement (pubM)
Result: **PASS**

## Endpoint

`POST /api/v1/publications/from-inventory` — contract `pubM.publications.fromInventory.v1`

## Source inventory

| Field | Value |
|---|---|
| inventoryId | `8778eb6a-dc5f-4cb6-9f3d-d06beaed6428` |
| breed | Cream Legbar |

## Generated publication

| Field | Value |
|---|---|
| publicationId | `1f86322e-ae19-4cab-8ec3-3a3e7d07a814` |
| sourceModule | inventoryManagement |
| sourceObjectId | `8778eb6a-dc5f-4cb6-9f3d-d06beaed6428` |
| titleSnapshot | Cream Legbar — legkip Oerse Kippies |
| currentStatus | DRAFT |
| correlationId | `8c24f4a2-7c18-3244-875d-c17128c0ba55` |

## Verification

Publication generated from validated inventory via commL forwarding.

## Commit

`8e7ee9a` — Add publication create-from-inventory endpoint for PM-1 flow.
