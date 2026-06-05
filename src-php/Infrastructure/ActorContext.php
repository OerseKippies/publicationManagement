<?php

declare(strict_types=1);

namespace PubM\Infrastructure;

final class ActorContext
{
    private const VALID_TYPES = ['USER', 'SERVICE_ACCOUNT', 'SYSTEM'];

    public function __construct(
        public readonly string $actorType,
        public readonly ?string $actorId
    ) {
    }

    public static function fromHeaders(?string $actorType, ?string $actorId): self
    {
        $type = strtoupper(trim((string) $actorType));
        if ($type === '' || !in_array($type, self::VALID_TYPES, true)) {
            return new self('SYSTEM', null);
        }

        $id = $actorId !== null && $actorId !== '' ? strtolower(trim($actorId)) : null;
        if ($id !== null && !Uuid::isValid($id)) {
            $id = null;
        }

        return new self($type, $id);
    }

    public function actorModule(): string
    {
        return $this->actorType;
    }
}
