<?php

declare(strict_types=1);

namespace PubM\Domain\Service;

use PubM\Audit\PublicationAuditLogger;
use PubM\Http\ApiException;
use PubM\Infrastructure\ActorContext;
use PubM\Infrastructure\Clock;
use PubM\Infrastructure\Database;
use PubM\Infrastructure\Uuid;
use PubM\Repository\PublicationRepository;
use PubM\Repository\PublicationScheduleRepository;

final class ScheduleService
{
    public function __construct(
        private readonly Database $database,
        private readonly PublicationRepository $publications,
        private readonly PublicationScheduleRepository $schedules,
        private readonly PublicationService $publicationService,
        private readonly PublicationAuditLogger $audit,
        private readonly Clock $clock
    ) {
    }

    /** @param array<string, mixed> $payload */
    public function schedule(string $publicationId, array $payload, ActorContext $actor, string $correlationId): array
    {
        if (!Uuid::isValid($publicationId)) {
            throw new ApiException('VALIDATION_ERROR', 'publication id must be a valid UUID', 400);
        }

        $publication = $this->publications->findById(strtolower($publicationId));
        if ($publication === null) {
            throw new ApiException('NOT_FOUND', 'publication not found', 404);
        }

        if (($publication['currentStatus'] ?? '') !== 'APPROVED') {
            throw new ApiException('INVALID_STATE', 'publication must be APPROVED to schedule', 409);
        }

        $scheduledAtRaw = trim((string) ($payload['scheduledAt'] ?? ''));
        if ($scheduledAtRaw === '') {
            throw new ApiException('VALIDATION_ERROR', 'scheduledAt is required (UTC format Y-m-d H:i:s)', 400);
        }

        $scheduledAt = gmdate('Y-m-d H:i:s', strtotime($scheduledAtRaw));
        if ($scheduledAt === false || $scheduledAt === '1970-01-01 00:00:00') {
            throw new ApiException('VALIDATION_ERROR', 'scheduledAt must be a valid datetime', 400);
        }

        $maxRetries = isset($payload['maxRetries']) ? (int) $payload['maxRetries'] : 3;
        if ($maxRetries < 0 || $maxRetries > 10) {
            throw new ApiException('VALIDATION_ERROR', 'maxRetries must be between 0 and 10', 400);
        }

        $existing = $this->schedules->findActiveByPublicationId(strtolower($publicationId));
        if ($existing !== null) {
            throw new ApiException('CONFLICT', 'publication already has an active schedule', 409);
        }

        $scheduleId = Uuid::v4();
        $now = $this->clock->nowUtc();
        $previous = (string) $publication['currentStatus'];

        $this->database->beginTransaction();
        try {
            $this->schedules->insert([
                'scheduleId' => $scheduleId,
                'publicationId' => strtolower($publicationId),
                'scheduledAt' => $scheduledAt,
                'scheduleStatus' => 'PENDING',
                'retryCount' => 0,
                'maxRetries' => $maxRetries,
                'lastError' => null,
                'processedAt' => null,
                'createdAt' => $now,
                'updatedAt' => $now,
            ]);

            $this->publications->update(strtolower($publicationId), ['currentStatus' => 'SCHEDULED'], $now);

            $this->audit->log(
                'Publication',
                strtolower($publicationId),
                'PUBLICATION_SCHEDULED',
                $actor,
                $correlationId,
                $previous,
                'SCHEDULED',
                'publication scheduled',
                ['scheduleId' => $scheduleId, 'scheduledAt' => $scheduledAt]
            );

            $this->database->commit();
        } catch (\Throwable $exception) {
            $this->database->rollBack();
            throw $exception;
        }

        return [
            'publication' => $this->publicationService->get($publicationId),
            'schedule' => $this->schedules->findActiveByPublicationId(strtolower($publicationId)),
        ];
    }

    /** @return array{processed: int, succeeded: int, failed: int, results: list<array<string, mixed>>} */
    public function processDueSchedules(?ActorContext $actor = null): array
    {
        $actor ??= ActorContext::fromHeaders('SYSTEM', null);
        $correlationId = Uuid::v4();
        $now = $this->clock->nowUtc();
        $due = $this->schedules->findDuePending($now);

        $results = [];
        $succeeded = 0;
        $failed = 0;

        foreach ($due as $schedule) {
            $scheduleId = (string) $schedule['scheduleId'];
            $publicationId = (string) $schedule['publicationId'];

            $this->schedules->update($scheduleId, ['scheduleStatus' => 'PROCESSING'], $now);

            try {
                $published = $this->publicationService->publish($publicationId, $actor, $correlationId, $scheduleId);
                $succeeded++;
                $results[] = [
                    'scheduleId' => $scheduleId,
                    'publicationId' => $publicationId,
                    'status' => 'COMPLETED',
                    'publicationStatus' => $published['currentStatus'] ?? null,
                ];
            } catch (\Throwable $exception) {
                $retryCount = (int) $schedule['retryCount'] + 1;
                $maxRetries = (int) $schedule['maxRetries'];
                $errorMessage = substr($exception->getMessage(), 0, 500);

                if ($retryCount > $maxRetries) {
                    $this->schedules->update($scheduleId, [
                        'scheduleStatus' => 'FAILED',
                        'retryCount' => $retryCount,
                        'lastError' => $errorMessage,
                        'processedAt' => $now,
                    ], $now);
                    $failed++;
                    $results[] = [
                        'scheduleId' => $scheduleId,
                        'publicationId' => $publicationId,
                        'status' => 'FAILED',
                        'error' => $errorMessage,
                    ];
                } else {
                    $this->schedules->update($scheduleId, [
                        'scheduleStatus' => 'PENDING',
                        'retryCount' => $retryCount,
                        'lastError' => $errorMessage,
                    ], $now);
                    $failed++;
                    $results[] = [
                        'scheduleId' => $scheduleId,
                        'publicationId' => $publicationId,
                        'status' => 'RETRY',
                        'retryCount' => $retryCount,
                        'error' => $errorMessage,
                    ];
                }
            }
        }

        return [
            'processed' => count($due),
            'succeeded' => $succeeded,
            'failed' => $failed,
            'results' => $results,
        ];
    }
}
