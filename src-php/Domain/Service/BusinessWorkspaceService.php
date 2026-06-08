<?php

declare(strict_types=1);

namespace PubM\Domain\Service;

use PubM\Http\ApiException;
use PubM\Infrastructure\ActorContext;
use PubM\Infrastructure\Clock;
use PubM\Infrastructure\Database;
use PubM\Infrastructure\Uuid;
use PubM\Repository\BusinessRepository;

final class BusinessWorkspaceService
{
    private const ALLOWED_TRANSITIONS = [
        'DRAFT' => ['PUBLISHED', 'ARCHIVED'],
        'PUBLISHED' => ['STALE', 'EXPIRED', 'ARCHIVED', 'REMOVED'],
        'STALE' => ['PUBLISHED', 'EXPIRED', 'ARCHIVED', 'REMOVED'],
        'EXPIRED' => ['PUBLISHED', 'ARCHIVED', 'REMOVED'],
        'ARCHIVED' => ['DRAFT'],
        'REMOVED' => [],
    ];

    public function __construct(
        private readonly Database $database,
        private readonly BusinessRepository $business,
        private readonly HealthEngineService $healthEngine,
        private readonly RenewalEngineService $renewalEngine,
        private readonly Clock $clock
    ) {
    }

    /** @return list<array<string, mixed>> */
    public function listPlatforms(): array
    {
        return $this->business->findAll('pubm_platforms', 'name ASC');
    }

    /** @param array<string, mixed> $payload */
    public function createFromAdvertisement(array $payload, ActorContext $actor): array
    {
        $adRef = strtolower(trim((string) ($payload['advertisementReference'] ?? $payload['advertisementId'] ?? '')));
        $platformId = strtolower(trim((string) ($payload['platformId'] ?? '')));
        if ($adRef === '' || !Uuid::isValid($adRef)) {
            throw new ApiException('VALIDATION_ERROR', 'advertisementReference must be a valid UUID', 400);
        }
        if ($platformId === '' || !Uuid::isValid($platformId)) {
            throw new ApiException('VALIDATION_ERROR', 'platformId must be a valid UUID', 400);
        }

        $now = $this->clock->nowUtc();
        $record = [
            'registryId' => Uuid::v4(),
            'publicationId' => isset($payload['publicationId']) ? strtolower((string) $payload['publicationId']) : null,
            'advertisementReference' => $adRef,
            'platformId' => $platformId,
            'publicationUrl' => trim((string) ($payload['publicationUrl'] ?? '')) ?: null,
            'externalIdentifier' => trim((string) ($payload['externalIdentifier'] ?? '')) ?: null,
            'publicationDate' => $payload['publicationDate'] ?? null,
            'renewalDate' => $payload['renewalDate'] ?? null,
            'registryStatus' => 'DRAFT',
            'notes' => trim((string) ($payload['notes'] ?? '')) ?: null,
            'createdAt' => $now,
            'updatedAt' => $now,
        ];
        $this->business->insert('pubm_publication_registry', $record);
        $this->recordStatusChange((string) $record['registryId'], null, 'DRAFT', $actor->actorId, 'Created from advertisement');
        $this->refreshHealth((string) $record['registryId']);

        return $record;
    }

    /** @return list<array<string, mixed>> */
    public function listRegistry(?string $advertisementReference = null): array
    {
        if ($advertisementReference !== null && $advertisementReference !== '') {
            return $this->business->registryByAdvertisement($advertisementReference);
        }

        return $this->business->findAll('pubm_publication_registry');
    }

    public function transition(string $registryId, string $toStatus, ActorContext $actor, ?string $reason = null): array
    {
        $registry = $this->requireRegistry($registryId);
        $from = strtoupper((string) $registry['registryStatus']);
        $to = strtoupper($toStatus);
        $allowed = self::ALLOWED_TRANSITIONS[$from] ?? [];
        if (!in_array($to, $allowed, true)) {
            throw new ApiException('CONFLICT', "transition from {$from} to {$to} not allowed", 409);
        }

        $now = $this->clock->nowUtc();
        $updates = ['registryStatus' => $to, 'updatedAt' => $now];
        if ($to === 'PUBLISHED' && empty($registry['publicationDate'])) {
            $updates['publicationDate'] = $now;
        }
        $this->business->update('pubm_publication_registry', 'registryId', strtolower($registryId), $updates);
        $this->recordStatusChange($registryId, $from, $to, $actor->actorId, $reason);
        $this->refreshHealth($registryId);

        return $this->requireRegistry($registryId);
    }

    public function renew(string $registryId, array $payload, ActorContext $actor): array
    {
        $registry = $this->requireRegistry($registryId);
        $renewalDate = (string) ($payload['renewalDate'] ?? gmdate('Y-m-d H:i:s', strtotime('+28 days')));
        $now = $this->clock->nowUtc();

        $renewal = [
            'renewalId' => Uuid::v4(),
            'registryId' => strtolower($registryId),
            'renewalDate' => $renewalDate,
            'status' => 'COMPLETED',
            'notes' => trim((string) ($payload['notes'] ?? 'Manual renewal')) ?: null,
            'createdAt' => $now,
            'completedAt' => $now,
        ];
        $this->business->insert('pubm_publication_renewals', $renewal);
        $this->business->update('pubm_publication_registry', 'registryId', strtolower($registryId), [
            'renewalDate' => $renewalDate,
            'registryStatus' => 'PUBLISHED',
            'updatedAt' => $now,
        ]);
        $this->recordStatusChange($registryId, (string) $registry['registryStatus'], 'PUBLISHED', $actor->actorId, 'Renewed');
        $this->refreshHealth($registryId);

        return $renewal;
    }

    /** @param array<string, mixed> $payload */
    public function saveMetrics(string $registryId, array $payload, ActorContext $actor): array
    {
        $this->requireRegistry($registryId);
        $record = [
            'metricId' => Uuid::v4(),
            'registryId' => strtolower($registryId),
            'views' => (int) ($payload['views'] ?? 0),
            'messages' => (int) ($payload['messages'] ?? 0),
            'favorites' => (int) ($payload['favorites'] ?? 0),
            'leads' => (int) ($payload['leads'] ?? 0),
            'conversions' => (int) ($payload['conversions'] ?? 0),
            'sales' => (int) ($payload['sales'] ?? 0),
            'recordedAt' => $this->clock->nowUtc(),
            'source' => strtoupper(trim((string) ($payload['source'] ?? 'MANUAL'))),
        ];
        $this->business->insert('pubm_publication_metrics', $record);

        return $record;
    }

    public function refreshHealth(string $registryId): void
    {
        $registry = $this->requireRegistry($registryId);
        $findings = $this->healthEngine->analyze($registry);
        $now = $this->clock->nowUtc();
        $pdo = $this->database->pdo();
        $pdo->prepare("DELETE FROM pubm_publication_health WHERE registryId = :id AND status = 'OPEN'")
            ->execute(['id' => strtolower($registryId)]);

        foreach ($findings as $finding) {
            $this->business->insert('pubm_publication_health', [
                'healthId' => Uuid::v4(),
                'registryId' => strtolower($registryId),
                'code' => $finding['code'],
                'severity' => $finding['severity'],
                'title' => $finding['title'],
                'detail' => $finding['detail'],
                'status' => 'OPEN',
                'createdAt' => $now,
                'resolvedAt' => null,
            ]);
        }
    }

    /** @return list<array<string, mixed>> */
    public function listHealth(?string $registryId = null): array
    {
        if ($registryId !== null) {
            $statement = $this->database->pdo()->prepare(
                'SELECT * FROM pubm_publication_health WHERE registryId = :id ORDER BY createdAt DESC'
            );
            $statement->execute(['id' => strtolower($registryId)]);

            return $statement->fetchAll() ?: [];
        }

        return $this->business->openHealthIssues();
    }

    /** @return list<array<string, mixed>> */
    public function listRenewals(): array
    {
        return $this->business->findAll('pubm_publication_renewals', 'renewalDate ASC');
    }

    /** @return array<string, mixed> */
    public function statisticsSummary(): array
    {
        $registry = $this->business->findAll('pubm_publication_registry');
        $platforms = $this->listPlatforms();
        $distribution = [];
        foreach ($registry as $row) {
            $pid = (string) ($row['platformId'] ?? 'unknown');
            $distribution[$pid] = ($distribution[$pid] ?? 0) + 1;
        }

        return [
            'publicationCount' => count($registry),
            'publishedCount' => $this->business->countByRegistryStatus('PUBLISHED'),
            'staleCount' => $this->business->countByRegistryStatus('STALE'),
            'expiredCount' => $this->business->countByRegistryStatus('EXPIRED'),
            'platformDistribution' => $distribution,
            'platformCount' => count($platforms),
        ];
    }

    /** @return array<string, mixed> */
    public function copilotDashboard(): array
    {
        $renewals = $this->business->renewalsDue(gmdate('Y-m-d H:i:s', strtotime('+7 days')));
        $renewalSummary = $this->renewalEngine->summarize($this->listRenewals());
        $healthIssues = $this->business->openHealthIssues();
        $stats = $this->statisticsSummary();
        $top = $this->database->pdo()->query(
            'SELECT r.*, m.views, m.leads FROM pubm_publication_registry r
             LEFT JOIN pubm_publication_metrics m ON m.registryId = r.registryId
             ORDER BY COALESCE(m.leads, 0) DESC, COALESCE(m.views, 0) DESC LIMIT 5'
        )->fetchAll() ?: [];

        return [
            'module' => 'pubM',
            'publication_count' => $stats['publicationCount'],
            'published_count' => $stats['publishedCount'],
            'stale_count' => $stats['staleCount'],
            'expired_count' => $stats['expiredCount'],
            'renewal_due_count' => count($renewals),
            'health_issue_count' => count($healthIssues),
            'widgets' => [
                'published' => $stats['publishedCount'],
                'expiringSoon' => $renewalSummary['thisWeek'],
                'expired' => $stats['expiredCount'],
                'needsRenewal' => $renewalSummary['overdue'] + $renewalSummary['today'],
                'healthIssues' => count($healthIssues),
                'topPublications' => $top,
                'platformDistribution' => $stats['platformDistribution'],
            ],
            'renewals' => $renewalSummary,
            'timestamp' => $this->clock->nowIso8601(),
        ];
    }

    /** @return array<string, mixed> */
    private function requireRegistry(string $registryId): array
    {
        $row = $this->business->findById('pubm_publication_registry', 'registryId', strtolower($registryId));
        if ($row === null) {
            throw new ApiException('NOT_FOUND', 'publication registry entry not found', 404);
        }

        return $row;
    }

    private function recordStatusChange(string $registryId, ?string $from, string $to, ?string $actorId, ?string $reason): void
    {
        $this->business->insert('pubm_publication_status_history', [
            'historyId' => Uuid::v4(),
            'registryId' => strtolower($registryId),
            'fromStatus' => $from,
            'toStatus' => $to,
            'actorId' => $actorId,
            'reason' => $reason,
            'createdAt' => $this->clock->nowUtc(),
        ]);
    }
}
