-- pubM core business schema (MariaDB 10.6)
-- Database: nl_module_pubM  |  User: nl_module_pubM
-- Sprint names map to pubm_* / existing Phase-2 tables.

CREATE TABLE IF NOT EXISTS pubm_schema_migrations (
    migrationId VARCHAR(64) NOT NULL PRIMARY KEY,
    appliedAt DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS pubm_platforms (
    platformId CHAR(36) NOT NULL PRIMARY KEY,
    name VARCHAR(128) NOT NULL,
    slug VARCHAR(64) NOT NULL,
    status VARCHAR(24) NOT NULL DEFAULT 'ACTIVE',
    mode VARCHAR(24) NOT NULL DEFAULT 'MANUAL',
    notes TEXT NULL,
    createdAt DATETIME NOT NULL,
    updatedAt DATETIME NOT NULL,
    UNIQUE KEY uq_pubm_platform_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS pubm_platform_accounts (
    accountId CHAR(36) NOT NULL PRIMARY KEY,
    platformId CHAR(36) NOT NULL,
    accountName VARCHAR(180) NOT NULL,
    status VARCHAR(24) NOT NULL DEFAULT 'ACTIVE',
    credentialsRef VARCHAR(255) NULL,
    createdAt DATETIME NOT NULL,
    updatedAt DATETIME NOT NULL,
    KEY idx_pubm_accounts_platform (platformId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS pubm_publication_registry (
    registryId CHAR(36) NOT NULL PRIMARY KEY,
    publicationId CHAR(36) NULL,
    advertisementReference CHAR(36) NOT NULL,
    platformId CHAR(36) NOT NULL,
    publicationUrl VARCHAR(512) NULL,
    externalIdentifier VARCHAR(180) NULL,
    publicationDate DATETIME NULL,
    renewalDate DATETIME NULL,
    registryStatus VARCHAR(24) NOT NULL DEFAULT 'DRAFT',
    notes TEXT NULL,
    createdAt DATETIME NOT NULL,
    updatedAt DATETIME NOT NULL,
    KEY idx_pubm_registry_ad (advertisementReference),
    KEY idx_pubm_registry_platform (platformId),
    KEY idx_pubm_registry_status (registryStatus),
    KEY idx_pubm_registry_renewal (renewalDate)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS pubm_publication_status_history (
    historyId CHAR(36) NOT NULL PRIMARY KEY,
    registryId CHAR(36) NOT NULL,
    fromStatus VARCHAR(24) NULL,
    toStatus VARCHAR(24) NOT NULL,
    actorId CHAR(36) NULL,
    reason VARCHAR(255) NULL,
    createdAt DATETIME NOT NULL,
    KEY idx_pubm_status_history_registry (registryId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS pubm_publication_metrics (
    metricId CHAR(36) NOT NULL PRIMARY KEY,
    registryId CHAR(36) NOT NULL,
    views INT NOT NULL DEFAULT 0,
    messages INT NOT NULL DEFAULT 0,
    favorites INT NOT NULL DEFAULT 0,
    leads INT NOT NULL DEFAULT 0,
    conversions INT NOT NULL DEFAULT 0,
    sales INT NOT NULL DEFAULT 0,
    recordedAt DATETIME NOT NULL,
    source VARCHAR(32) NOT NULL DEFAULT 'MANUAL',
    KEY idx_pubm_metrics_registry (registryId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS pubm_publication_health (
    healthId CHAR(36) NOT NULL PRIMARY KEY,
    registryId CHAR(36) NOT NULL,
    code VARCHAR(64) NOT NULL,
    severity VARCHAR(16) NOT NULL,
    title VARCHAR(180) NOT NULL,
    detail TEXT NOT NULL,
    status VARCHAR(24) NOT NULL DEFAULT 'OPEN',
    createdAt DATETIME NOT NULL,
    resolvedAt DATETIME NULL,
    KEY idx_pubm_health_registry (registryId),
    KEY idx_pubm_health_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS pubm_publication_renewals (
    renewalId CHAR(36) NOT NULL PRIMARY KEY,
    registryId CHAR(36) NOT NULL,
    renewalDate DATETIME NOT NULL,
    status VARCHAR(24) NOT NULL DEFAULT 'SCHEDULED',
    notes TEXT NULL,
    createdAt DATETIME NOT NULL,
    completedAt DATETIME NULL,
    KEY idx_pubm_renewals_registry (registryId),
    KEY idx_pubm_renewals_date (renewalDate)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS pubm_publication_notes (
    noteId CHAR(36) NOT NULL PRIMARY KEY,
    registryId CHAR(36) NOT NULL,
    note TEXT NOT NULL,
    authorId CHAR(36) NULL,
    createdAt DATETIME NOT NULL,
    KEY idx_pubm_notes_registry (registryId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS pubm_activity_log (
    activityId CHAR(36) NOT NULL PRIMARY KEY,
    entityType VARCHAR(64) NOT NULL,
    entityId CHAR(36) NOT NULL,
    action VARCHAR(64) NOT NULL,
    detail JSON NULL,
    actorId CHAR(36) NULL,
    createdAt DATETIME NOT NULL,
    KEY idx_pubm_activity_entity (entityType, entityId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS pubm_settings (
    settingKey VARCHAR(64) NOT NULL PRIMARY KEY,
    settingValue JSON NOT NULL,
    updatedAt DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
