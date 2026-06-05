<?php

declare(strict_types=1);

namespace PubM\Repository;

use PubM\Infrastructure\Database;

final class PublicationChannelRepository
{
    public function __construct(private readonly Database $database)
    {
    }

    /** @return array<string, mixed>|null */
    public function findById(string $channelId): ?array
    {
        $statement = $this->database->pdo()->prepare(
            'SELECT channelId, channelName, channelType, configJson, createdAt, updatedAt
             FROM publication_channels WHERE channelId = :channelId LIMIT 1'
        );
        $statement->execute(['channelId' => strtolower($channelId)]);
        $row = $statement->fetch();

        return $row === false ? null : $row;
    }

    /** @param array<string, mixed> $record */
    public function insert(array $record): void
    {
        $sql = 'INSERT INTO publication_channels
            (channelId, channelName, channelType, configJson, createdAt, updatedAt)
            VALUES
            (:channelId, :channelName, :channelType, :configJson, :createdAt, :updatedAt)';

        $statement = $this->database->pdo()->prepare($sql);
        $statement->execute([
            'channelId' => strtolower((string) $record['channelId']),
            'channelName' => $record['channelName'],
            'channelType' => $record['channelType'],
            'configJson' => $record['configJson'],
            'createdAt' => $record['createdAt'],
            'updatedAt' => $record['updatedAt'],
        ]);
    }
}
