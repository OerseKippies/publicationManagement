# HANDOVER-PUBM-MVP

Date: 2026-06-08  
Module: publicationManagement (pubM)

## Delivered

- `migrations/001_pubM_core_schema.sql` + `004_pubM_business_schema.sql`
- Platform registry (Marktplaats, 2dehands, Facebook Marketplace, Website, Other)
- Publication registry with adM reference integration
- Lifecycle: publish, renew, archive, expire, remove
- Health engine + renewal engine
- Co-Pilot dashboard: `GET /api/copilot/dashboard`
- Seed: Cream Legbar, Marans, Faverolles, Kuikenstartpakket, VitalBoost, Maagkiezel, Wormenmix
- PHP production runtime + Flask dev (`pubm/app.py`)

## Deploy

```bash
php scripts/migrate.php
php scripts/seed.php
bash scripts/deploy_pubm_versio.sh
```

## Verify

```bash
php scripts/test_pubm_mvp.php
curl -sk https://pubm.oerse-kippies.nl/health
```

## Review Status

READY FOR OK-CORE REVIEW
