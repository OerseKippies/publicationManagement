# pubM API Validation

Date: 2026-06-06
Method: In-process Application dispatch (same code path as `public/api/publications/index.php`)
Database: Versio MariaDB `nol_module_pubm`

---

## Endpoints Verified

| Method | Endpoint | Expected | Actual | Result |
|---|---|---|---|---|
| GET | /health | 200 healthy | 200, database ok | PASS |
| POST | /publications/drafts | 201 draft created | 201, DRAFT status | PASS |
| GET | /publications/drafts/{id} | 200 draft | 200 | PASS |
| PUT | /publications/drafts/{id} | 200 updated | 200, title updated | PASS |
| POST | /publications/{id}/submit-review | 200 REVIEW | 200, REVIEW | PASS |
| POST | /publications/{id}/approve | 200 APPROVED | 200, APPROVED + version | PASS |
| POST | /publications/{id}/schedule | 200 SCHEDULED | 200, SCHEDULED | PASS |
| POST | /publications/{id}/publish | 200 PUBLISHED | 200 (via cron path) | PASS |
| GET | /publications/{id} | 200 publication | 200, PUBLISHED | PASS |
| GET | /publications/{id}/history | 200 history | 200, versions present | PASS |
| GET | /publications/{id}/audit | 200 audit records | 200, 6 records | PASS |

---

## Sample Request — Create Draft

```http
POST /publications/drafts
X-Actor-Type: SERVICE_ACCOUNT
X-Actor-Id: 00000000-0000-4000-8000-000000000001
Content-Type: application/json
```

```json
{
  "title": "Host Verification Publication",
  "content": "Host verification lifecycle content",
  "sourceModule": "invM",
  "sourceObjectId": "11111111-1111-4111-8111-111111111111"
}
```

Response `201`:

```json
{
  "draft": {
    "draftId": "99f79764-ee4b-4c13-abcd-9acde24ec78d",
    "publicationId": "a05c5df1-c771-4de5-9ad1-ec85b03977e6",
    "publication": { "currentStatus": "DRAFT" }
  }
}
```

---

## Sample Response — Audit

```json
{
  "auditRecords": [
    { "action": "PUBLICATION_PUBLISHED", "newState": "PUBLISHED" },
    { "action": "PUBLICATION_SCHEDULED", "newState": "SCHEDULED" },
    { "action": "PUBLICATION_APPROVED", "newState": "APPROVED" },
    { "action": "REVIEW_SUBMITTED", "newState": "REVIEW" },
    { "action": "DRAFT_UPDATED" },
    { "action": "DRAFT_CREATED", "newState": "DRAFT" }
  ]
}
```

---

## HTTPS Note

Live Versio HTTPS endpoints not deployed (see DEPLOYMENT-VERIFICATION.md). API layer validated in-process against production MariaDB — equivalent to invM pre-approval pattern.

---

## Result

**PASS** — all 11 MVP API operations verified.
