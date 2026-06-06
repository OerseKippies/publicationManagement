# pubM Deployment Verification

Date: 2026-06-06
Module: publicationManagement (pubM)
Target: VERSIO_HOSTED
Commits verified: a5b9b0d, 3abbb63 (+ host verification fixes)

---

## Deployment Status

| Item | Status | Evidence |
|---|---|---|
| Runtime code present | **PASS** | `src-php/`, `public/api/`, `scripts/` |
| Schema migrations | **PASS** | `schemas/001_publications.sql` applied |
| Config template | **PASS** | `config/config.example.php` |
| Local config (gitignored) | **PASS** | `config/config.php` — not in git |
| MariaDB target | **PASS** | Host `185.175.200.60`, database `nol_module_pubm` |
| PHP runtime | **PASS** | PHP 8.3.31 CLI |
| Versio HTTPS HTTP deploy | **CONDITIONAL** | No live endpoint at probed URLs (404 / DNS) |
| Git push to origin/main | **PENDING** | Commits local at verification time |

---

## Reference Commits

| Commit | Description |
|---|---|
| a5b9b0d | MVP Phase 2 runtime implementation |
| 3abbb63 | config.example.php template |

---

## Probed HTTPS Endpoints

| URL | Result |
|---|---|
| https://oerse-kippies.nl/public/api/health.php | 404 Not Found |
| https://oerse-kippies.nl/public/api/publications/ | 404 Not Found |
| https://pubm.oerse-kippies.nl/ | DNS not resolved |

**Note:** Runtime validated via PHP CLI against Versio MariaDB and in-process API (`scripts/host_verification.php`). Versio git-deploy of `public/api/*` recommended before OK-Core approval (same pattern as invM APR-2026-06-06-022 conditions).

---

## Deployment Completeness

| Component | Deployed |
|---|---|
| Database schema on Versio MariaDB | Yes |
| Runtime services (CLI) | Yes |
| Migration tracking | Yes |
| Cron script present | Yes (not cron-scheduled on host yet) |
| HTTP entrypoints on Versio | No |

---

## Result

**CONDITIONAL PASS** — core runtime operational on Versio MariaDB stack; Versio HTTPS deployment pending.
