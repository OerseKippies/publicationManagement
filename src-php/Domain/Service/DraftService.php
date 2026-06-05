<?php

declare(strict_types=1);

namespace PubM\Domain\Service;

use PubM\Audit\PublicationAuditLogger;
use PubM\Http\ApiException;
use PubM\Infrastructure\ActorContext;
use PubM\Infrastructure\Clock;
use PubM\Infrastructure\CommLGateway;
use PubM\Infrastructure\Database;
use PubM\Infrastructure\Uuid;
use PubM\Repository\PublicationChannelRepository;
use PubM\Repository\PublicationDraftRepository;
use PubM\Repository\PublicationRepository;
use PubM\Repository\PublicationTargetRepository;

final class DraftService
{
    public function __construct(
        private readonly Database $database,
        private readonly PublicationRepository $publications,
        private readonly PublicationDraftRepository $drafts,
        private readonly PublicationChannelRepository $channels,
        private readonly PublicationTargetRepository $targets,
        private readonly PublicationAuditLogger $audit,
        private readonly CommLGateway $comml,
        private readonly Clock $clock
    ) {
    }

    /** @param array<string, mixed> $payload */
    public function create(array $payload, ActorContext $actor, string $correlationId): array
    {
        $title = trim((string) ($payload['title'] ?? ''));
        $content = trim((string) ($payload['content'] ?? ''));
        if ($title === '' || $content === '') {
            throw new ApiException('VALIDATION_ERROR', 'title and content are required', 400);
        }

        $sourceModule = isset($payload['sourceModule']) ? trim((string) $payload['sourceModule']) : null;
        $sourceObjectId = isset($payload['sourceObjectId']) ? strtolower(trim((string) $payload['sourceObjectId'])) : null;
        if ($sourceObjectId !== null && $sourceObjectId !== '' && !Uuid::isValid($sourceObjectId)) {
            throw new ApiException('VALIDATION_ERROR', 'sourceObjectId must be a valid UUID', 400);
        }

        if ($sourceObjectId !== null && $sourceObjectId !== '') {
            try {
                $this->comml->validateMasterDataReference('inventoryItem', $sourceObjectId, $correlationId);
            } catch (\InvalidArgumentException $exception) {
                throw new ApiException('VALIDATION_ERROR', $exception->getMessage(), 400);
            }
        }

        $templateId = isset($payload['templateId']) ? strtolower(trim((string) $payload['templateId'])) : null;
        if ($templateId !== null && $templateId !== '' && !Uuid::isValid($templateId)) {
            throw new ApiException('VALIDATION_ERROR', 'templateId must be a valid UUID', 400);
        }

        $channelId = isset($payload['channelId']) ? strtolower(trim((string) $payload['channelId'])) : null;
        if ($channelId !== null && $channelId !== '' && !Uuid::isValid($channelId)) {
            throw new ApiException('VALIDATION_ERROR', 'channelId must be a valid UUID', 400);
        }

        $targetReference = isset($payload['targetReference']) ? trim((string) $payload['targetReference']) : null;

        $publicationId = Uuid::v4();
        $draftId = Uuid::v4();
        $now = $this->clock->nowUtc();

        $this->database->beginTransaction();
        try {
            $this->publications->insert([
                'publicationId' => $publicationId,
                'sourceModule' => $sourceModule === '' ? null : $sourceModule,
                'sourceObjectId' => $sourceObjectId === '' ? null : $sourceObjectId,
                'currentStatus' => 'DRAFT',
                'activeVersionId' => null,
                'createdAt' => $now,
                'updatedAt' => $now,
            ]);

            $this->drafts->insert([
                'draftId' => $draftId,
                'publicationId' => $publicationId,
                'draftState' => 'EDITING',
                'titleSnapshot' => $title,
                'contentSnapshot' => $content,
                'templateId' => $templateId === '' ? null : $templateId,
                'channelId' => $channelId === '' ? null : $channelId,
                'createdByActorType' => $actor->actorType,
                'createdByActorId' => $actor->actorId,
                'updatedByActorType' => $actor->actorType,
                'updatedByActorId' => $actor->actorId,
                'createdAt' => $now,
                'updatedAt' => $now,
            ]);

            if ($channelId !== null && $channelId !== '' && $targetReference !== null && $targetReference !== '') {
                if ($this->channels->findById($channelId) === null) {
                    throw new ApiException('VALIDATION_ERROR', 'channelId not found', 400);
                }

                $this->targets->insert([
                    'targetId' => Uuid::v4(),
                    'publicationId' => $publicationId,
                    'channelId' => $channelId,
                    'targetReference' => $targetReference,
                    'createdAt' => $now,
                ]);
            }

            $this->audit->log(
                'PublicationDraft',
                $draftId,
                'DRAFT_CREATED',
                $actor,
                $correlationId,
                null,
                'DRAFT',
                'draft created',
                ['publicationId' => $publicationId, 'title' => $title]
            );

            $this->database->commit();
        } catch (\Throwable $exception) {
            $this->database->rollBack();
            throw $exception;
        }

        return $this->get($draftId);
    }

    public function get(string $draftId): array
    {
        if (!Uuid::isValid($draftId)) {
            throw new ApiException('VALIDATION_ERROR', 'draft id must be a valid UUID', 400);
        }

        $draft = $this->drafts->findById(strtolower($draftId));
        if ($draft === null) {
            throw new ApiException('NOT_FOUND', 'publication draft not found', 404);
        }

        $publication = $this->publications->findById((string) $draft['publicationId']);
        $draft['publication'] = $publication;
        $draft['targets'] = $this->targets->findByPublicationId((string) $draft['publicationId']);

        return $draft;
    }

    /** @param array<string, mixed> $payload */
    public function update(string $draftId, array $payload, ActorContext $actor, string $correlationId): array
    {
        $existing = $this->get($draftId);
        $publication = $existing['publication'] ?? null;
        if (!is_array($publication)) {
            throw new ApiException('NOT_FOUND', 'publication not found for draft', 404);
        }

        if (($publication['currentStatus'] ?? '') !== 'DRAFT') {
            throw new ApiException('INVALID_STATE', 'draft can only be updated while publication is DRAFT', 409);
        }

        $updates = [];
        $details = [];

        if (array_key_exists('title', $payload)) {
            $title = trim((string) $payload['title']);
            if ($title === '') {
                throw new ApiException('VALIDATION_ERROR', 'title cannot be empty', 400);
            }
            $updates['titleSnapshot'] = $title;
            $details['title'] = $title;
        }

        if (array_key_exists('content', $payload)) {
            $content = trim((string) $payload['content']);
            if ($content === '') {
                throw new ApiException('VALIDATION_ERROR', 'content cannot be empty', 400);
            }
            $updates['contentSnapshot'] = $content;
            $details['contentUpdated'] = true;
        }

        if (array_key_exists('templateId', $payload)) {
            $templateId = strtolower(trim((string) $payload['templateId']));
            if ($templateId !== '' && !Uuid::isValid($templateId)) {
                throw new ApiException('VALIDATION_ERROR', 'templateId must be a valid UUID', 400);
            }
            $updates['templateId'] = $templateId === '' ? null : $templateId;
            $details['templateId'] = $updates['templateId'];
        }

        if ($updates === []) {
            throw new ApiException('VALIDATION_ERROR', 'no updatable fields provided', 400);
        }

        $updates['updatedByActorType'] = $actor->actorType;
        $updates['updatedByActorId'] = $actor->actorId;
        $now = $this->clock->nowUtc();

        $this->database->beginTransaction();
        try {
            $this->drafts->update((string) $existing['draftId'], $updates, $now);
            $this->publications->update((string) $publication['publicationId'], [], $now);
            $this->audit->log(
                'PublicationDraft',
                (string) $existing['draftId'],
                'DRAFT_UPDATED',
                $actor,
                $correlationId,
                'DRAFT',
                'DRAFT',
                'draft updated',
                $details
            );
            $this->database->commit();
        } catch (\Throwable $exception) {
            $this->database->rollBack();
            throw $exception;
        }

        return $this->get($draftId);
    }
}
