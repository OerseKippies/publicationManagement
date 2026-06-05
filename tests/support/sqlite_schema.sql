-- pubM SQLite schema for automated tests

CREATE TABLE IF NOT EXISTS pubm_schema_migrations (
    migrationId TEXT NOT NULL PRIMARY KEY,
    appliedAt TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS publications (
    publicationId TEXT NOT NULL PRIMARY KEY,
    sourceModule TEXT NULL,
    sourceObjectId TEXT NULL,
    currentStatus TEXT NOT NULL,
    activeVersionId TEXT NULL,
    createdAt TEXT NOT NULL,
    updatedAt TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS publication_drafts (
    draftId TEXT NOT NULL PRIMARY KEY,
    publicationId TEXT NOT NULL UNIQUE,
    draftState TEXT NOT NULL,
    titleSnapshot TEXT NOT NULL,
    contentSnapshot TEXT NOT NULL,
    templateId TEXT NULL,
    channelId TEXT NULL,
    createdByActorType TEXT NOT NULL,
    createdByActorId TEXT NULL,
    updatedByActorType TEXT NOT NULL,
    updatedByActorId TEXT NULL,
    createdAt TEXT NOT NULL,
    updatedAt TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS publication_templates (
    templateId TEXT NOT NULL PRIMARY KEY,
    templateName TEXT NOT NULL,
    contentTemplate TEXT NOT NULL,
    createdAt TEXT NOT NULL,
    updatedAt TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS publication_channels (
    channelId TEXT NOT NULL PRIMARY KEY,
    channelName TEXT NOT NULL,
    channelType TEXT NOT NULL,
    configJson TEXT NULL,
    createdAt TEXT NOT NULL,
    updatedAt TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS publication_targets (
    targetId TEXT NOT NULL PRIMARY KEY,
    publicationId TEXT NOT NULL,
    channelId TEXT NOT NULL,
    targetReference TEXT NOT NULL,
    createdAt TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS publication_schedules (
    scheduleId TEXT NOT NULL PRIMARY KEY,
    publicationId TEXT NOT NULL,
    scheduledAt TEXT NOT NULL,
    scheduleStatus TEXT NOT NULL,
    retryCount INTEGER NOT NULL DEFAULT 0,
    maxRetries INTEGER NOT NULL DEFAULT 3,
    lastError TEXT NULL,
    processedAt TEXT NULL,
    createdAt TEXT NOT NULL,
    updatedAt TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS publication_versions (
    versionId TEXT NOT NULL PRIMARY KEY,
    publicationId TEXT NOT NULL,
    versionNumber INTEGER NOT NULL,
    titleSnapshot TEXT NOT NULL,
    contentSnapshot TEXT NOT NULL,
    statusAtVersion TEXT NOT NULL,
    createdByActorType TEXT NOT NULL,
    createdByActorId TEXT NULL,
    createdAt TEXT NOT NULL,
    UNIQUE (publicationId, versionNumber)
);

CREATE TABLE IF NOT EXISTS publication_audit_records (
    auditRecordId TEXT NOT NULL PRIMARY KEY,
    entityType TEXT NOT NULL,
    entityId TEXT NOT NULL,
    action TEXT NOT NULL,
    previousState TEXT NULL,
    newState TEXT NULL,
    actorModule TEXT NOT NULL,
    actorIdRef TEXT NULL,
    correlationId TEXT NOT NULL,
    reason TEXT NULL,
    detailsJson TEXT NULL,
    createdAt TEXT NOT NULL
);
