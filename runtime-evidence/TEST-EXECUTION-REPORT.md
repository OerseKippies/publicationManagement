# pubM Test Execution Report

Date: 2026-06-06
Command: `D:\Programs\PHP\php.exe tests/run.php`
Environment: PHP 8.3.31, Versio MariaDB `nol_module_pubm` (pdo_mysql)

---

## Results

| Test | Status |
|---|---|
| Uuid v4 format | **PASS** |
| Uuid rejects invalid | **PASS** |
| lifecycle draft to published | **PASS** |
| schedule cron processing | **PASS** |
| audit trail immutability | **PASS** |
| persistence reload | **PASS** |

**Total: 6 passed, 0 failed, 0 skipped**

---

## Additional Scripts

| Script | Result |
|---|---|
| scripts/migrate.php | PASS |
| scripts/verify_runtime.php | PASS (5/5 steps) |
| scripts/host_verification.php | PASS |
| scripts/validate_schema.php | PASS |

---

## Environment Notes

| Item | Detail |
|---|---|
| pdo_sqlite | Not available — tests fall back to MariaDB config |
| pdo_mysql | Available — used for integration tests |
| SQLite in-memory tests | Skipped (driver unavailable) |

Test harness updated: `tests/run.php` uses `config/config.php` when `pdo_sqlite` unavailable.

---

## Result

**PASS** — all tests executed successfully against Versio MariaDB.
