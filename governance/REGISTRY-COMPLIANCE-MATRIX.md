# pubM Registry Compliance Matrix

Date: 2026-06-05
Module: publicationManagement (pubM)
Module Type: Runtime Module
Registry Version: 1.0.0
Authority: OK-Core/governance/REQUIRED-DOCUMENTATION-REGISTRY.md

---

## Runtime Module — Full Matrix

| Required Artifact | Required Version | Present | Missing | Compliant | Evidence |
|---|---|---|---|---|---|
| README.md | REQUIRED | Yes | — | Yes | OerseKippies/publicationManagement/README.md |
| ARCHITECTURE.md | REQUIRED | Yes | — | Yes | OerseKippies/publicationManagement/ARCHITECTURE.md |
| MODULE-SCOPE.md | REQUIRED | Yes | — | Yes | OerseKippies/publicationManagement/MODULE-SCOPE.md |
| CHANGELOG.md | OPTIONAL | Yes | — | N/A | OerseKippies/publicationManagement/CHANGELOG.md |
| ADRs/ | REQUIRED (Architecture Foundation) | Yes | — | Yes | OerseKippies/publicationManagement/ADRs/ |
| handover/ | CONDITIONAL (RFA submission) | Yes | — | Yes | OerseKippies/publicationManagement/handover/ |
| architecture/DOD-VALIDATION.md | CONDITIONAL (MVP approval) | Yes | — | Yes | OerseKippies/publicationManagement/architecture/DOD-VALIDATION.md |
| compliance/MODULE-COMPLIANCE.md | REQUIRED (after adoption) | Yes | — | Yes | OerseKippies/publicationManagement/compliance/MODULE-COMPLIANCE.md |
| compliance/DOCUMENTATION-REGISTRY-CONSUMPTION-LOG.md | REQUIRED (after adoption) | Yes | — | Yes | OerseKippies/publicationManagement/compliance/DOCUMENTATION-REGISTRY-CONSUMPTION-LOG.md |
| compliance/MANDATORY-READING-CONSUMPTION-LOG.md | REQUIRED (ADR-CORE-0034) | Yes | — | Yes | OerseKippies/publicationManagement/compliance/MANDATORY-READING-CONSUMPTION-LOG.md |
| contracts/ or docs/api/ | REQUIRED (API draft) | Yes | — | Yes | OerseKippies/publicationManagement/contracts/, public/api/ |
| schemas/ or persistence docs | CONDITIONAL (before persistence impl) | Yes | — | Yes | OerseKippies/publicationManagement/schemas/MARIADB-SCHEMA-DRAFT.md |
| architecture/OWNERSHIP-NAMING-MAP.md | CONDITIONAL (mapping ADR) | N/A | — | N/A | OK-Core ADR-0027 + OWNERSHIP-CLARIFICATION-PUBM.md |

---

## Summary

| Metric | Value |
|---|---|
| REQUIRED items | 9/9 present |
| OPTIONAL items | 1/1 present |
| CONDITIONAL items (applicable) | 3/3 satisfied |
| Registry compliance | **100%** |

---

## Registry Authority Reference

```text
Registry: OK-Core/governance/REQUIRED-DOCUMENTATION-REGISTRY.md
Version: 1.0.0
Effective: 2026-06-05
Module Type: Runtime Module
Consumption Log: compliance/DOCUMENTATION-REGISTRY-CONSUMPTION-LOG.md
```

Modules must not duplicate the registry. This matrix inventories compliance only.
