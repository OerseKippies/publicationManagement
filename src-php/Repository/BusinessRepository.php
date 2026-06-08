<?php

declare(strict_types=1);

namespace PubM\Repository;

use PubM\Infrastructure\Database;

final class BusinessRepository
{
    public function __construct(private readonly Database $database)
    {
    }

    /** @param array<string, mixed> $record */
    public function insert(string $table, array $record): void
    {
        $columns = array_keys($record);
        $placeholders = array_map(static fn (string $c): string => ':' . $c, $columns);
        $sql = sprintf('INSERT INTO %s (%s) VALUES (%s)', $table, implode(', ', $columns), implode(', ', $placeholders));
        $this->database->pdo()->prepare($sql)->execute($record);
    }

    /** @return array<string, mixed>|null */
    public function findById(string $table, string $idColumn, string $id): ?array
    {
        $statement = $this->database->pdo()->prepare("SELECT * FROM {$table} WHERE {$idColumn} = :id LIMIT 1");
        $statement->execute(['id' => $id]);
        $row = $statement->fetch();

        return $row === false ? null : $row;
    }

    /** @return list<array<string, mixed>> */
    public function findAll(string $table, ?string $orderBy = 'createdAt DESC'): array
    {
        $sql = "SELECT * FROM {$table}" . ($orderBy ? " ORDER BY {$orderBy}" : '');
        $statement = $this->database->pdo()->query($sql);

        return $statement === false ? [] : $statement->fetchAll();
    }

    /** @param array<string, mixed> $updates */
    public function update(string $table, string $idColumn, string $id, array $updates): void
    {
        $sets = [];
        $params = ['id' => $id];
        foreach ($updates as $key => $value) {
            $sets[] = "{$key} = :{$key}";
            $params[$key] = $value;
        }
        $this->database->pdo()->prepare("UPDATE {$table} SET " . implode(', ', $sets) . " WHERE {$idColumn} = :id")->execute($params);
    }

    public function countByRegistryStatus(string $status): int
    {
        $statement = $this->database->pdo()->prepare(
            'SELECT COUNT(*) AS c FROM pubm_publication_registry WHERE registryStatus = :status'
        );
        $statement->execute(['status' => $status]);
        $row = $statement->fetch();

        return (int) ($row['c'] ?? 0);
    }

    /** @return list<array<string, mixed>> */
    public function openHealthIssues(): array
    {
        $statement = $this->database->pdo()->query(
            "SELECT * FROM pubm_publication_health WHERE status = 'OPEN' ORDER BY createdAt DESC"
        );

        return $statement === false ? [] : $statement->fetchAll();
    }

    /** @return list<array<string, mixed>> */
    public function renewalsDue(string $beforeDate): array
    {
        $statement = $this->database->pdo()->prepare(
            "SELECT * FROM pubm_publication_renewals WHERE status = 'SCHEDULED' AND renewalDate <= :before ORDER BY renewalDate ASC"
        );
        $statement->execute(['before' => $beforeDate]);

        return $statement->fetchAll() ?: [];
    }

    /** @return list<array<string, mixed>> */
    public function registryByAdvertisement(string $advertisementId): array
    {
        $statement = $this->database->pdo()->prepare(
            'SELECT * FROM pubm_publication_registry WHERE advertisementReference = :ad ORDER BY createdAt DESC'
        );
        $statement->execute(['ad' => strtolower($advertisementId)]);

        return $statement->fetchAll() ?: [];
    }
}
