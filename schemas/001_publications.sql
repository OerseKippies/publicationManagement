-- pubM Phase 2 schema (MariaDB 10.6)
-- Module: publicationManagement (pubM)

CREATE TABLE IF NOT EXISTS pubm_schema_migrations (
    migrationId VARCHAR(64) NOT NULL PRIMARY KEY,
    appliedAt DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS publications (
    publicationId CHAR(36) NOT NULL PRIMARY KEY,
    sourceModule VARCHAR(64) NULL,
    sourceObjectId CHAR(36) NULL,
    currentStatus VARCHAR(24) NOT NULL,
    activeVersionId CHAR(36) NULL,
    createdAt DATETIME NOT NULL,
    updatedAt DATETIME NOT NULL,
    KEY idx_publications_status (currentStatus),
    KEY idx_publications_source (sourceModule, sourceObjectId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS publication_drafts (
    draftId CHAR(36) NOT NULL PRIMARY KEY,
    publicationId CHAR(36) NOT NULL,
    draftState VARCHAR(24) NOT NULL,
    titleSnapshot VARCHAR(512) NOT NULL,
    contentSnapshot TEXT NOT NULL,
    templateId CHAR(36) NULL,
    channelId CHAR(36) NULL,
    createdByActorType VARCHAR(24) NOT NULL,
    createdByActorId CHAR(36) NULL,
    updatedByActorType VARCHAR(24) NOT NULL,
    updatedByActorId CHAR(36) NULL,
    createdAt DATETIME NOT NULL,
    updatedAt DATETIME NOT NULL,
    UNIQUE KEY uq_publication_drafts_publication (publicationId),
    KEY idx_publication_drafts_state (draftState),
    CONSTRAINT fk_publication_drafts_publication
        FOREIGN KEY (publicationId) REFERENCES publications (publicationId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS publication_templates (
    templateId CHAR(36) NOT NULL PRIMARY KEY,
    templateName VARCHAR(128) NOT NULL,
    contentTemplate TEXT NOT NULL,
    createdAt DATETIME NOT NULL,
    updatedAt DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS publication_channels (
    channelId CHAR(36) NOT NULL PRIMARY KEY,
    channelName VARCHAR(128) NOT NULL,
    channelType VARCHAR(64) NOT NULL,
    configJson JSON NULL,
    createdAt DATETIME NOT NULL,
    updatedAt DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS publication_targets (
    targetId CHAR(36) NOT NULL PRIMARY KEY,
    publicationId CHAR(36) NOT NULL,
    channelId CHAR(36) NOT NULL,
    targetReference VARCHAR(255) NOT NULL,
    createdAt DATETIME NOT NULL,
    KEY idx_publication_targets_publication (publicationId),
    CONSTRAINT fk_publication_targets_publication
        FOREIGN KEY (publicationId) REFERENCES publications (publicationId),
    CONSTRAINT fk_publication_targets_channel
        FOREIGN KEY (channelId) REFERENCES publication_channels (channelId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS publication_schedules (
    scheduleId CHAR(36) NOT NULL PRIMARY KEY,
    publicationId CHAR(36) NOT NULL,
    scheduledAt DATETIME NOT NULL,
    scheduleStatus VARCHAR(24) NOT NULL,
    retryCount INT NOT NULL DEFAULT 0,
    maxRetries INT NOT NULL DEFAULT 3,
    lastError VARCHAR(512) NULL,
    processedAt DATETIME NULL,
    createdAt DATETIME NOT NULL,
    updatedAt DATETIME NOT NULL,
    KEY idx_publication_schedules_due (scheduleStatus, scheduledAt),
    KEY idx_publication_schedules_publication (publicationId),
    CONSTRAINT fk_publication_schedules_publication
        FOREIGN KEY (publicationId) REFERENCES publications (publicationId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS publication_versions (
    versionId CHAR(36) NOT NULL PRIMARY KEY,
    publicationId CHAR(36) NOT NULL,
    versionNumber INT NOT NULL,
    titleSnapshot VARCHAR(512) NOT NULL,
    contentSnapshot TEXT NOT NULL,
    statusAtVersion VARCHAR(24) NOT NULL,
    createdByActorType VARCHAR(24) NOT NULL,
    createdByActorId CHAR(36) NULL,
    createdAt DATETIME NOT NULL,
    UNIQUE KEY uq_publication_versions_number (publicationId, versionNumber),
    KEY idx_publication_versions_publication (publicationId),
    CONSTRAINT fk_publication_versions_publication
        FOREIGN KEY (publicationId) REFERENCES publications (publicationId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS publication_audit_records (
    auditRecordId CHAR(36) NOT NULL PRIMARY KEY,
    entityType VARCHAR(64) NOT NULL,
    entityId CHAR(36) NOT NULL,
    action VARCHAR(64) NOT NULL,
    previousState VARCHAR(64) NULL,
    newState VARCHAR(64) NULL,
    actorModule VARCHAR(24) NOT NULL,
    actorIdRef CHAR(36) NULL,
    correlationId CHAR(36) NOT NULL,
    reason VARCHAR(255) NULL,
    detailsJson JSON NULL,
    createdAt DATETIME NOT NULL,
    KEY idx_publication_audit_entity (entityType, entityId),
    KEY idx_publication_audit_correlation (correlationId),
    KEY idx_publication_audit_publication (entityId, createdAt)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
