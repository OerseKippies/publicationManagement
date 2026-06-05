<?php

declare(strict_types=1);

namespace PubM\Repository;

use PubM\Infrastructure\Database;

final class PublicationVersionRepository
{
    public function __construct(private readonly Database $database)
    {
    }

    /** @param array<string, mixed> $record */
    public function insert(array $record): void
    {
        $sql = 'INSERT INTO publication_versions
            (versionId, publicationId, versionNumber, titleSnapshot, contentSnapshot, statusAtVersion,
             createdByActorType, createdByActorId, createdAt)
            VALUES
            (:versionId, :publicationId, :versionNumber, :titleSnapshot, :contentSnapshot, :statusAtVersion,
             :createdByActorType, :createdByActorId, :createdAt)';

        $statement = $this->database->pdo()->prepare($sql);
        $statement->execute([
            'versionId' => strtolower((string) $record['versionId']),
            'publicationId' => strtolower((string) $record['publicationId']),
            'versionNumber' => $record['versionNumber'],
            'titleSnapshot' => $record['titleSnapshot'],
            'contentSnapshot' => $record['contentSnapshot'],
            'statusAtVersion' => $record['statusAtVersion'],
            'createdByActorType' => $record['createdByActorType'],
            'createdByActorId' => $record['createdByActorId'],
            'createdAt' => $record['createdAt'],
        ]);
    }

    public function nextVersionNumber(string $publicationId): int
    {
        $statement = $this->database->pdo()->prepare(
            'SELECT COALESCE(MAX(versionNumber), 0) + 1 AS nextVersion
             FROM publication_versions WHERE publicationId = :publicationId'
        );
        $statement->execute(['publicationId' => strtolower($publicationId)]);
        $row = $statement->fetch();

        return (int) ($row['nextVersion'] ?? 1);
    }

    /** @return list<array<string, mixed>> */
    public function findByPublicationId(string $publicationId): array
    {
        $statement = $this->database->pdo()->prepare(
            'SELECT versionId, publicationId, versionNumber, titleSnapshot, contentSnapshot, statusAtVersion,
                    createdByActorType, createdByActorId, createdAt
             FROM publication_versions
             WHERE publicationId = :publicationId
             ORDER BY versionNumber ASC'
        );
        $statement->execute(['publicationId' => strtolower($publicationId)]);

        return $statement->fetchAll();
    }
}
