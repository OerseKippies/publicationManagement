<?php

declare(strict_types=1);

namespace PubM\Repository;

use PubM\Infrastructure\Database;

final class PublicationRepository
{
    public function __construct(private readonly Database $database)
    {
    }

    /** @param array<string, mixed> $record */
    public function insert(array $record): void
    {
        $sql = 'INSERT INTO publications
            (publicationId, sourceModule, sourceObjectId, currentStatus, activeVersionId, createdAt, updatedAt)
            VALUES
            (:publicationId, :sourceModule, :sourceObjectId, :currentStatus, :activeVersionId, :createdAt, :updatedAt)';

        $statement = $this->database->pdo()->prepare($sql);
        $statement->execute([
            'publicationId' => strtolower((string) $record['publicationId']),
            'sourceModule' => $record['sourceModule'],
            'sourceObjectId' => $record['sourceObjectId'],
            'currentStatus' => $record['currentStatus'],
            'activeVersionId' => $record['activeVersionId'],
            'createdAt' => $record['createdAt'],
            'updatedAt' => $record['updatedAt'],
        ]);
    }

    /** @param array<string, mixed> $updates */
    public function update(string $publicationId, array $updates, string $updatedAt): void
    {
        $sets = ['updatedAt = :updatedAt'];
        $params = [
            'publicationId' => strtolower($publicationId),
            'updatedAt' => $updatedAt,
        ];

        foreach (['currentStatus', 'activeVersionId', 'sourceModule', 'sourceObjectId'] as $field) {
            if (array_key_exists($field, $updates)) {
                $sets[] = "{$field} = :{$field}";
                $params[$field] = $updates[$field];
            }
        }

        $sql = 'UPDATE publications SET ' . implode(', ', $sets) . ' WHERE publicationId = :publicationId';
        $statement = $this->database->pdo()->prepare($sql);
        $statement->execute($params);
    }

    /** @return array<string, mixed>|null */
    public function findById(string $publicationId): ?array
    {
        $statement = $this->database->pdo()->prepare(
            'SELECT publicationId, sourceModule, sourceObjectId, currentStatus, activeVersionId, createdAt, updatedAt
             FROM publications WHERE publicationId = :publicationId LIMIT 1'
        );
        $statement->execute(['publicationId' => strtolower($publicationId)]);
        $row = $statement->fetch();

        return $row === false ? null : $row;
    }

    /** @return list<array<string, mixed>> */
    public function list(?string $sourceModule = null, ?string $sourceObjectId = null, int $limit = 100): array
    {
        $sql = 'SELECT publicationId, sourceModule, sourceObjectId, currentStatus, activeVersionId, createdAt, updatedAt
                FROM publications WHERE 1=1';
        $params = [];

        if ($sourceModule !== null && $sourceModule !== '') {
            $sql .= ' AND sourceModule = :sourceModule';
            $params['sourceModule'] = $sourceModule;
        }

        if ($sourceObjectId !== null && $sourceObjectId !== '') {
            $sql .= ' AND sourceObjectId = :sourceObjectId';
            $params['sourceObjectId'] = strtolower($sourceObjectId);
        }

        $sql .= ' ORDER BY createdAt DESC LIMIT ' . max(1, min($limit, 500));
        $statement = $this->database->pdo()->prepare($sql);
        $statement->execute($params);

        return $statement->fetchAll() ?: [];
    }
}
