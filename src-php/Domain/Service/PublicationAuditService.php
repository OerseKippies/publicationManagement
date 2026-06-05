<?php

declare(strict_types=1);

namespace PubM\Domain\Service;

use PubM\Audit\PublicationAuditRepository;
use PubM\Http\ApiException;
use PubM\Infrastructure\Uuid;

final class PublicationAuditService
{
    public function __construct(private readonly PublicationAuditRepository $audit)
    {
    }

    /** @return list<array<string, mixed>> */
    public function listForPublication(string $publicationId, int $limit = 100): array
    {
        if (!Uuid::isValid($publicationId)) {
            throw new ApiException('VALIDATION_ERROR', 'publication id must be a valid UUID', 400);
        }

        return $this->audit->findByPublication(strtolower($publicationId), $limit);
    }
}
