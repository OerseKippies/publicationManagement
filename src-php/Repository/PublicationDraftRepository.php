<?php

declare(strict_types=1);

namespace PubM\Repository;

use PubM\Infrastructure\Database;

final class PublicationDraftRepository
{
    public function __construct(private readonly Database $database)
    {
    }

    /** @param array<string, mixed> $record */
    public function insert(array $record): void
    {
        $sql = 'INSERT INTO publication_drafts
            (draftId, publicationId, draftState, titleSnapshot, contentSnapshot, templateId, channelId,
             createdByActorType, createdByActorId, updatedByActorType, updatedByActorId, createdAt, updatedAt)
            VALUES
            (:draftId, :publicationId, :draftState, :titleSnapshot, :contentSnapshot, :templateId, :channelId,
             :createdByActorType, :createdByActorId, :updatedByActorType, :updatedByActorId, :createdAt, :updatedAt)';

        $statement = $this->database->pdo()->prepare($sql);
        $statement->execute([
            'draftId' => strtolower((string) $record['draftId']),
            'publicationId' => strtolower((string) $record['publicationId']),
            'draftState' => $record['draftState'],
            'titleSnapshot' => $record['titleSnapshot'],
            'contentSnapshot' => $record['contentSnapshot'],
            'templateId' => $record['templateId'],
            'channelId' => $record['channelId'],
            'createdByActorType' => $record['createdByActorType'],
            'createdByActorId' => $record['createdByActorId'],
            'updatedByActorType' => $record['updatedByActorType'],
            'updatedByActorId' => $record['updatedByActorId'],
            'createdAt' => $record['createdAt'],
            'updatedAt' => $record['updatedAt'],
        ]);
    }

    /** @param array<string, mixed> $updates */
    public function update(string $draftId, array $updates, string $updatedAt): void
    {
        $sets = ['updatedAt = :updatedAt'];
        $params = [
            'draftId' => strtolower($draftId),
            'updatedAt' => $updatedAt,
        ];

        foreach ([
            'draftState', 'titleSnapshot', 'contentSnapshot', 'templateId', 'channelId',
            'updatedByActorType', 'updatedByActorId',
        ] as $field) {
            if (array_key_exists($field, $updates)) {
                $sets[] = "{$field} = :{$field}";
                $params[$field] = $updates[$field];
            }
        }

        $sql = 'UPDATE publication_drafts SET ' . implode(', ', $sets) . ' WHERE draftId = :draftId';
        $statement = $this->database->pdo()->prepare($sql);
        $statement->execute($params);
    }

    /** @return array<string, mixed>|null */
    public function findById(string $draftId): ?array
    {
        $statement = $this->database->pdo()->prepare(
            'SELECT draftId, publicationId, draftState, titleSnapshot, contentSnapshot, templateId, channelId,
                    createdByActorType, createdByActorId, updatedByActorType, updatedByActorId, createdAt, updatedAt
             FROM publication_drafts WHERE draftId = :draftId LIMIT 1'
        );
        $statement->execute(['draftId' => strtolower($draftId)]);
        $row = $statement->fetch();

        return $row === false ? null : $row;
    }

    /** @return array<string, mixed>|null */
    public function findByPublicationId(string $publicationId): ?array
    {
        $statement = $this->database->pdo()->prepare(
            'SELECT draftId, publicationId, draftState, titleSnapshot, contentSnapshot, templateId, channelId,
                    createdByActorType, createdByActorId, updatedByActorType, updatedByActorId, createdAt, updatedAt
             FROM publication_drafts WHERE publicationId = :publicationId LIMIT 1'
        );
        $statement->execute(['publicationId' => strtolower($publicationId)]);
        $row = $statement->fetch();

        return $row === false ? null : $row;
    }
}
