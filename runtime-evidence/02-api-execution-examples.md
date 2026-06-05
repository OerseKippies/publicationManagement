# pubM Runtime Evidence — API Execution Examples

Date: 2026-06-06
Module: publicationManagement (pubM)
Entry point: `public/api/publications/index.php`

Headers (all requests):

```http
Content-Type: application/json
X-Correlation-Id: 22222222-2222-4222-8222-222222222222
X-Actor-Type: USER
X-Actor-Id: aaaaaaaa-aaaa-4aaa-8aaa-aaaaaaaaaaaa
```

---

## 1. Create Publication Draft

```http
POST /publications/drafts
```

```json
{
  "title": "Broiler Batch Listing",
  "content": "Available inventory batch for publication",
  "sourceModule": "invM",
  "sourceObjectId": "11111111-1111-4111-8111-111111111111"
}
```

Response `201`:

```json
{
  "draft": {
    "draftId": "...",
    "publicationId": "...",
    "draftState": "EDITING",
    "titleSnapshot": "Broiler Batch Listing",
    "publication": { "currentStatus": "DRAFT" }
  }
}
```

---

## 2. Update Draft

```http
PUT /publications/drafts/{draftId}
```

```json
{
  "title": "Updated Broiler Batch Listing",
  "content": "Revised content"
}
```

---

## 3. Submit For Review

```http
POST /publications/{publicationId}/submit-review
```

Response: `currentStatus` → `REVIEW`

---

## 4. Approve Publication

```http
POST /publications/{publicationId}/approve
```

Response: `currentStatus` → `APPROVED`, `activeVersionId` set

---

## 5. Schedule Publication

```http
POST /publications/{publicationId}/schedule
```

```json
{
  "scheduledAt": "2026-06-07 08:00:00",
  "maxRetries": 3
}
```

Response: `currentStatus` → `SCHEDULED`

---

## 6. Publish (Immediate)

```http
POST /publications/{publicationId}/publish
```

Response: `currentStatus` → `PUBLISHED`

---

## 7. Get Publication

```http
GET /publications/{publicationId}
```

---

## 8. Get History

```http
GET /publications/{publicationId}/history
```

Returns publication, draft, versions, schedule, targets.

---

## 9. Get Audit Trail

```http
GET /publications/{publicationId}/audit
```

Returns immutable audit records for all mutations.

---

## Health Check

```http
GET /health
```

Via `public/api/health.php`
