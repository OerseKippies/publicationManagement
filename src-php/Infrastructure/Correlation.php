<?php

declare(strict_types=1);

namespace PubM\Infrastructure;

final class Correlation
{
    public static function resolve(?string $headerValue): string
    {
        if ($headerValue !== null && $headerValue !== '' && Uuid::isValid($headerValue)) {
            return strtolower($headerValue);
        }

        return Uuid::v4();
    }
}
