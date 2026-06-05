<?php

declare(strict_types=1);

namespace PubM\Domain\Service;

use PubM\Audit\PublicationAuditLogger;
use PubM\Http\ApiException;
use PubM\Infrastructure\ActorContext;
use PubM\Infrastructure\Clock;
use PubM\Infrastructure\Database;
use PubM\Infrastructure\Uuid;
use PubM\Repository\PublicationDraftRepository;
use PubM\Repository\PublicationRepository;
use PubM\Repository\PublicationScheduleRepository;
use PubM\Repository\PublicationTargetRepository;
use PubM\Repository\PublicationVersionRepository;

final class PublicationService
{
    public function __construct(
        private readonly Database $database,
        private readonly PublicationRepository $publications,
        private readonly PublicationDraftRepository $drafts,
        private readonly PublicationVersionRepository $versions,
        private readonly PublicationScheduleRepository $schedules,
        private readonly PublicationTargetRepository $targets,
        private readonly PublicationAuditLogger $audit,
        private readonly Clock $clock
    ) {
    }

    /** @return array<string, mixed> */
    public function get(string $publicationId): array
    {
        if (!Uuid::isValid($publicationId)) {
            throw new ApiException('VALIDATION_ERROR', 'publication id must be a valid UUID', 400);
        }

        $publication = $this->publications->findById(strtolower($publicationId));
        if ($publication === null) {
            throw new ApiException('NOT_FOUND', 'publication not found', 404);
        }

        $publication['draft'] = $this->drafts->findByPublicationId(strtolower($publicationId));
        $publication['schedule'] = $this->schedules->findActiveByPublicationId(strtolower($publicationId));
        $publication['targets'] = $this->targets->findByPublicationId(strtolower($publicationId));

        if ($publication['activeVersionId'] !== null) {
            $versionList = $this->versions->findByPublicationId(strtolower($publicationId));
            foreach ($versionList as $version) {
                if ($version['versionId'] === $publication['activeVersionId']) {
                    $publication['activeVersion'] = $version;
                    break;
                }
            }
        }

        return $publication;
    }

    public function submitForReview(string $publicationId, ActorContext $actor, string $correlationId): array
    {
        $publication = $this->requirePublication($publicationId);
        $this->assertTransition((string) $publication['currentStatus'], 'REVIEW');

        $draft = $this->drafts->findByPublicationId(strtolower($publicationId));
        if ($draft === null) {
            throw new ApiException('NOT_FOUND', 'draft not found for publication', 404);
        }

        $now = $this->clock->nowUtc();
        $previous = (string) $publication['currentStatus'];

        $this->database->beginTransaction();
        try {
            $this->publications->update(strtolower($publicationId), ['currentStatus' => 'REVIEW'], $now);
            $this->drafts->update((string) $draft['draftId'], [
                'draftState' => 'SUBMITTED',
                'updatedByActorType' => $actor->actorType,
                'updatedByActorId' => $actor->actorId,
            ], $now);
            $this->audit->log(
                'Publication',
                strtolower($publicationId),
                'REVIEW_SUBMITTED',
                $actor,
                $correlationId,
                $previous,
                'REVIEW',
                'draft submitted for review'
            );
            $this->database->commit();
        } catch (\Throwable $exception) {
            $this->database->rollBack();
            throw $exception;
        }

        return $this->get($publicationId);
    }

    public function approve(string $publicationId, ActorContext $actor, string $correlationId): array
    {
        $publication = $this->requirePublication($publicationId);
        $this->assertTransition((string) $publication['currentStatus'], 'APPROVED');

        $draft = $this->drafts->findByPublicationId(strtolower($publicationId));
        if ($draft === null) {
            throw new ApiException('NOT_FOUND', 'draft not found for publication', 404);
        }

        $now = $this->clock->nowUtc();
        $previous = (string) $publication['currentStatus'];
        $versionId = Uuid::v4();
        $versionNumber = $this->versions->nextVersionNumber(strtolower($publicationId));

        $this->database->beginTransaction();
        try {
            $this->versions->insert([
                'versionId' => $versionId,
                'publicationId' => strtolower($publicationId),
                'versionNumber' => $versionNumber,
                'titleSnapshot' => (string) $draft['titleSnapshot'],
                'contentSnapshot' => (string) $draft['contentSnapshot'],
                'statusAtVersion' => 'APPROVED',
                'createdByActorType' => $actor->actorType,
                'createdByActorId' => $actor->actorId,
                'createdAt' => $now,
            ]);

            $this->publications->update(strtolower($publicationId), [
                'currentStatus' => 'APPROVED',
                'activeVersionId' => $versionId,
            ], $now);

            $this->drafts->update((string) $draft['draftId'], [
                'draftState' => 'APPROVED',
                'updatedByActorType' => $actor->actorType,
                'updatedByActorId' => $actor->actorId,
            ], $now);

            $this->audit->log(
                'Publication',
                strtolower($publicationId),
                'PUBLICATION_APPROVED',
                $actor,
                $correlationId,
                $previous,
                'APPROVED',
                'publication approved',
                ['versionId' => $versionId, 'versionNumber' => $versionNumber]
            );

            $this->database->commit();
        } catch (\Throwable $exception) {
            $this->database->rollBack();
            throw $exception;
        }

        return $this->get($publicationId);
    }

    public function publish(string $publicationId, ActorContext $actor, string $correlationId, ?string $scheduleId = null): array
    {
        $publication = $this->requirePublication($publicationId);
        $status = (string) $publication['currentStatus'];
        if (!in_array($status, ['APPROVED', 'SCHEDULED'], true)) {
            throw new ApiException('INVALID_STATE', 'publication must be APPROVED or SCHEDULED to publish', 409);
        }

        $draft = $this->drafts->findByPublicationId(strtolower($publicationId));
        if ($draft === null) {
            throw new ApiException('NOT_FOUND', 'draft not found for publication', 404);
        }

        $now = $this->clock->nowUtc();
        $previous = $status;
        $versionId = $publication['activeVersionId'] ?? null;

        if ($versionId === null) {
            $versionId = Uuid::v4();
            $versionNumber = $this->versions->nextVersionNumber(strtolower($publicationId));
        } else {
            $versionNumber = null;
        }

        $this->database->beginTransaction();
        try {
            if ($versionNumber !== null) {
                $this->versions->insert([
                    'versionId' => $versionId,
                    'publicationId' => strtolower($publicationId),
                    'versionNumber' => $versionNumber,
                    'titleSnapshot' => (string) $draft['titleSnapshot'],
                    'contentSnapshot' => (string) $draft['contentSnapshot'],
                    'statusAtVersion' => 'PUBLISHED',
                    'createdByActorType' => $actor->actorType,
                    'createdByActorId' => $actor->actorId,
                    'createdAt' => $now,
                ]);
            } else {
                $versionList = $this->versions->findByPublicationId(strtolower($publicationId));
                foreach ($versionList as $version) {
                    if ($version['versionId'] === $versionId) {
                        $versionNumber = (int) $version['versionNumber'];
                        break;
                    }
                }
            }

            $this->publications->update(strtolower($publicationId), [
                'currentStatus' => 'PUBLISHED',
                'activeVersionId' => $versionId,
            ], $now);

            if ($scheduleId !== null) {
                $this->schedules->update($scheduleId, [
                    'scheduleStatus' => 'COMPLETED',
                    'processedAt' => $now,
                    'lastError' => null,
                ], $now);
            } elseif ($status === 'SCHEDULED') {
                $activeSchedule = $this->schedules->findActiveByPublicationId(strtolower($publicationId));
                if ($activeSchedule !== null) {
                    $this->schedules->update((string) $activeSchedule['scheduleId'], [
                        'scheduleStatus' => 'COMPLETED',
                        'processedAt' => $now,
                        'lastError' => null,
                    ], $now);
                }
            }

            $this->audit->log(
                'Publication',
                strtolower($publicationId),
                'PUBLICATION_PUBLISHED',
                $actor,
                $correlationId,
                $previous,
                'PUBLISHED',
                'publication published',
                ['versionId' => $versionId, 'versionNumber' => $versionNumber]
            );

            $this->database->commit();
        } catch (\Throwable $exception) {
            $this->database->rollBack();
            throw $exception;
        }

        return $this->get($publicationId);
    }

    /** @return array<string, mixed> */
    private function requirePublication(string $publicationId): array
    {
        if (!Uuid::isValid($publicationId)) {
            throw new ApiException('VALIDATION_ERROR', 'publication id must be a valid UUID', 400);
        }

        $publication = $this->publications->findById(strtolower($publicationId));
        if ($publication === null) {
            throw new ApiException('NOT_FOUND', 'publication not found', 404);
        }

        return $publication;
    }

    private function assertTransition(string $from, string $to): void
    {
        $allowed = [
            'DRAFT' => ['REVIEW'],
            'REVIEW' => ['APPROVED'],
            'APPROVED' => ['SCHEDULED', 'PUBLISHED'],
            'SCHEDULED' => ['PUBLISHED', 'APPROVED'],
        ];

        if (!isset($allowed[$from]) || !in_array($to, $allowed[$from], true)) {
            throw new ApiException(
                'INVALID_STATE',
                sprintf('cannot transition from %s to %s', $from, $to),
                409
            );
        }
    }
}
