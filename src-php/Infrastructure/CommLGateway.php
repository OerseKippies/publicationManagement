<?php

declare(strict_types=1);

namespace PubM\Infrastructure;

/**
 * commL boundary gateway — all cross-module calls route through communicationLayer.
 * MVP: stub validation when commL is disabled; live HTTP when configured.
 */
final class CommLGateway
{
    public function __construct(private readonly Config $config)
    {
    }

    /** @param array<string, mixed> $payload */
    public function validateMasterDataReference(string $referenceType, string $referenceId, string $correlationId): void
    {
        if (!$this->config->getBool('comml.enabled', false)) {
            if (!Uuid::isValid($referenceId)) {
                throw new \InvalidArgumentException('referenceId must be a valid UUID');
            }

            return;
        }

        $baseUrl = rtrim($this->config->getString('comml.base_url'), '/');
        $url = $baseUrl . '/api/comml/route/mdM/validate-reference';

        $body = json_encode([
            'referenceType' => $referenceType,
            'referenceId' => $referenceId,
            'correlationId' => $correlationId,
        ], JSON_THROW_ON_ERROR);

        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/json\r\nX-Correlation-Id: {$correlationId}\r\n",
                'content' => $body,
                'timeout' => $this->config->getInt('comml.timeout_seconds', 5),
                'ignore_errors' => true,
            ],
        ]);

        $response = @file_get_contents($url, false, $context);
        if ($response === false) {
            throw new \RuntimeException('commL master data validation request failed');
        }

        $decoded = json_decode($response, true);
        if (!is_array($decoded) || ($decoded['valid'] ?? false) !== true) {
            throw new \InvalidArgumentException('master data reference validation failed via commL');
        }
    }
}
