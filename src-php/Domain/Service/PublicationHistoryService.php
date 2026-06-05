<?php

declare(strict_types=1);

namespace PubM\Domain\Service;

use PubM\Http\ApiException;
use PubM\Infrastructure\Uuid;
use PubM\Repository\PublicationDraftRepository;
use PubM\Repository\PublicationRepository;
use PubM\Repository\PublicationScheduleRepository;
use PubM\Repository\PublicationTargetRepository;
use PubM\Repository\PublicationVersionRepository;

final class PublicationHistoryService
{
    public function __construct(
        private readonly PublicationRepository $publications,
        private readonly PublicationDraftRepository $drafts,
        private readonly PublicationVersionRepository $versions,
        private readonly PublicationScheduleRepository $schedules,
        private readonly PublicationTargetRepository $targets
    ) {
    }

    /** @return array<string, mixed> */
    public function getHistory(string $publicationId): array
    {
        if (!Uuid::isValid($publicationId)) {
            throw new ApiException('VALIDATION_ERROR', 'publication id must be a valid UUID', 400);
        }

        $publication = $this->publications->findById(strtolower($publicationId));
        if ($publication === null) {
            throw new ApiException('NOT_FOUND', 'publication not found', 404);
        }

        return [
            'publication' => $publication,
            'draft' => $this->drafts->findByPublicationId(strtolower($publicationId)),
            'versions' => $this->versions->findByPublicationId(strtolower($publicationId)),
            'schedule' => $this->schedules->findActiveByPublicationId(strtolower($publicationId)),
            'targets' => $this->targets->findByPublicationId(strtolower($publicationId)),
        ];
    }
}
