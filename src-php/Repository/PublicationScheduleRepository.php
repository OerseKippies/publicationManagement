<?php

declare(strict_types=1);

namespace PubM\Repository;

use PubM\Infrastructure\Database;

final class PublicationScheduleRepository
{
    public function __construct(private readonly Database $database)
    {
    }

    /** @param array<string, mixed> $record */
    public function insert(array $record): void
    {
        $sql = 'INSERT INTO publication_schedules
            (scheduleId, publicationId, scheduledAt, scheduleStatus, retryCount, maxRetries,
             lastError, processedAt, createdAt, updatedAt)
            VALUES
            (:scheduleId, :publicationId, :scheduledAt, :scheduleStatus, :retryCount, :maxRetries,
             :lastError, :processedAt, :createdAt, :updatedAt)';

        $statement = $this->database->pdo()->prepare($sql);
        $statement->execute([
            'scheduleId' => strtolower((string) $record['scheduleId']),
            'publicationId' => strtolower((string) $record['publicationId']),
            'scheduledAt' => $record['scheduledAt'],
            'scheduleStatus' => $record['scheduleStatus'],
            'retryCount' => $record['retryCount'],
            'maxRetries' => $record['maxRetries'],
            'lastError' => $record['lastError'],
            'processedAt' => $record['processedAt'],
            'createdAt' => $record['createdAt'],
            'updatedAt' => $record['updatedAt'],
        ]);
    }

    /** @param array<string, mixed> $updates */
    public function update(string $scheduleId, array $updates, string $updatedAt): void
    {
        $sets = ['updatedAt = :updatedAt'];
        $params = [
            'scheduleId' => strtolower($scheduleId),
            'updatedAt' => $updatedAt,
        ];

        foreach (['scheduleStatus', 'retryCount', 'lastError', 'processedAt'] as $field) {
            if (array_key_exists($field, $updates)) {
                $sets[] = "{$field} = :{$field}";
                $params[$field] = $updates[$field];
            }
        }

        $sql = 'UPDATE publication_schedules SET ' . implode(', ', $sets) . ' WHERE scheduleId = :scheduleId';
        $statement = $this->database->pdo()->prepare($sql);
        $statement->execute($params);
    }

    /** @return array<string, mixed>|null */
    public function findActiveByPublicationId(string $publicationId): ?array
    {
        $statement = $this->database->pdo()->prepare(
            'SELECT scheduleId, publicationId, scheduledAt, scheduleStatus, retryCount, maxRetries,
                    lastError, processedAt, createdAt, updatedAt
             FROM publication_schedules
             WHERE publicationId = :publicationId AND scheduleStatus IN (\'PENDING\', \'PROCESSING\')
             ORDER BY createdAt DESC LIMIT 1'
        );
        $statement->execute(['publicationId' => strtolower($publicationId)]);
        $row = $statement->fetch();

        return $row === false ? null : $row;
    }

    /** @return list<array<string, mixed>> */
    public function findDuePending(string $asOfUtc, int $limit = 50): array
    {
        $statement = $this->database->pdo()->prepare(
            'SELECT scheduleId, publicationId, scheduledAt, scheduleStatus, retryCount, maxRetries,
                    lastError, processedAt, createdAt, updatedAt
             FROM publication_schedules
             WHERE scheduleStatus = \'PENDING\' AND scheduledAt <= :asOfUtc
             ORDER BY scheduledAt ASC LIMIT :limit'
        );
        $statement->bindValue(':asOfUtc', $asOfUtc);
        $statement->bindValue(':limit', max(1, min($limit, 100)), \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }
}
