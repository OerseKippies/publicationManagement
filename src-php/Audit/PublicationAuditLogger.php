<?php

declare(strict_types=1);

namespace PubM\Audit;

use PubM\Infrastructure\ActorContext;
use PubM\Infrastructure\Clock;

final class PublicationAuditLogger
{
    public function __construct(
        private readonly PublicationAuditRepository $repository,
        private readonly Clock $clock
    ) {
    }

    /** @param array<string, mixed>|null $details */
    public function log(
        string $entityType,
        string $entityId,
        string $action,
        ActorContext $actor,
        string $correlationId,
        ?string $previousState = null,
        ?string $newState = null,
        ?string $reason = null,
        ?array $details = null
    ): void {
        $this->repository->insert(
            $entityType,
            $entityId,
            $action,
            $previousState,
            $newState,
            $actor->actorModule(),
            $actor->actorId,
            $correlationId,
            $reason,
            $this->clock->nowUtc(),
            $details
        );
    }
}
