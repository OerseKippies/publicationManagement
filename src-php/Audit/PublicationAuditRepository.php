<?php

declare(strict_types=1);

namespace PubM\Audit;

use PubM\Infrastructure\Database;
use PubM\Infrastructure\Uuid;

final class PublicationAuditRepository
{
    public function __construct(private readonly Database $database)
    {
    }

    /** @param array<string, mixed>|null $details */
    public function insert(
        string $entityType,
        string $entityId,
        string $action,
        ?string $previousState,
        ?string $newState,
        string $actorModule,
        ?string $actorIdRef,
        string $correlationId,
        ?string $reason,
        string $createdAt,
        ?array $details = null
    ): void {
        $sql = 'INSERT INTO publication_audit_records
            (auditRecordId, entityType, entityId, action, previousState, newState,
             actorModule, actorIdRef, correlationId, reason, detailsJson, createdAt)
            VALUES
            (:auditRecordId, :entityType, :entityId, :action, :previousState, :newState,
             :actorModule, :actorIdRef, :correlationId, :reason, :detailsJson, :createdAt)';

        $statement = $this->database->pdo()->prepare($sql);
        $statement->execute([
            'auditRecordId' => Uuid::v4(),
            'entityType' => $entityType,
            'entityId' => strtolower($entityId),
            'action' => $action,
            'previousState' => $previousState,
            'newState' => $newState,
            'actorModule' => $actorModule,
            'actorIdRef' => $actorIdRef,
            'correlationId' => $correlationId,
            'reason' => $reason,
            'detailsJson' => $details === null ? null : json_encode($details, JSON_THROW_ON_ERROR),
            'createdAt' => $createdAt,
        ]);
    }

    /** @return list<array<string, mixed>> */
    public function findByPublication(string $publicationId, int $limit = 100): array
    {
        return $this->findAll('Publication', $publicationId, null, $limit);
    }

    /** @return list<array<string, mixed>> */
    public function findAll(
        ?string $entityType = null,
        ?string $entityId = null,
        ?string $correlationId = null,
        int $limit = 100
    ): array {
        $conditions = [];
        $params = ['limit' => max(1, min($limit, 500))];

        if ($entityType !== null && $entityType !== '') {
            $conditions[] = 'entityType = :entityType';
            $params['entityType'] = $entityType;
        }

        if ($entityId !== null && $entityId !== '') {
            $conditions[] = 'entityId = :entityId';
            $params['entityId'] = strtolower($entityId);
        }

        if ($correlationId !== null && $correlationId !== '') {
            $conditions[] = 'correlationId = :correlationId';
            $params['correlationId'] = $correlationId;
        }

        $sql = 'SELECT auditRecordId, entityType, entityId, action, previousState, newState,
                       actorModule, actorIdRef, correlationId, reason, detailsJson, createdAt
                FROM publication_audit_records';

        if ($conditions !== []) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }

        $sql .= ' ORDER BY createdAt DESC LIMIT :limit';

        $statement = $this->database->pdo()->prepare($sql);
        foreach ($params as $key => $value) {
            $statement->bindValue(':' . $key, $value, is_int($value) ? \PDO::PARAM_INT : \PDO::PARAM_STR);
        }
        $statement->execute();

        return array_map(static function (array $row): array {
            if ($row['detailsJson'] !== null) {
                $decoded = json_decode((string) $row['detailsJson'], true);
                $row['details'] = is_array($decoded) ? $decoded : null;
            } else {
                $row['details'] = null;
            }
            unset($row['detailsJson']);

            return $row;
        }, $statement->fetchAll());
    }
}
