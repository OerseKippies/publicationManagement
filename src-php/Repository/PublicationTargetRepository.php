<?php

declare(strict_types=1);

namespace PubM\Repository;

use PubM\Infrastructure\Database;

final class PublicationTargetRepository
{
    public function __construct(private readonly Database $database)
    {
    }

    /** @param array<string, mixed> $record */
    public function insert(array $record): void
    {
        $sql = 'INSERT INTO publication_targets
            (targetId, publicationId, channelId, targetReference, createdAt)
            VALUES
            (:targetId, :publicationId, :channelId, :targetReference, :createdAt)';

        $statement = $this->database->pdo()->prepare($sql);
        $statement->execute([
            'targetId' => strtolower((string) $record['targetId']),
            'publicationId' => strtolower((string) $record['publicationId']),
            'channelId' => strtolower((string) $record['channelId']),
            'targetReference' => $record['targetReference'],
            'createdAt' => $record['createdAt'],
        ]);
    }

    /** @return list<array<string, mixed>> */
    public function findByPublicationId(string $publicationId): array
    {
        $statement = $this->database->pdo()->prepare(
            'SELECT targetId, publicationId, channelId, targetReference, createdAt
             FROM publication_targets WHERE publicationId = :publicationId'
        );
        $statement->execute(['publicationId' => strtolower($publicationId)]);

        return $statement->fetchAll();
    }
}
