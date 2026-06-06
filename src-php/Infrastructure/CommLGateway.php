<?php

declare(strict_types=1);

namespace PubM\Infrastructure;

/**
 * commL boundary gateway — all cross-module calls route through communicationLayer.
 */
final class CommLGateway
{
    public function __construct(private readonly Config $config)
    {
    }

    public function validateMasterDataReference(string $referenceType, string $referenceId, string $correlationId): void
    {
        if (!$this->config->getBool('comml.enabled', false)) {
            if (!Uuid::isValid($referenceId)) {
                throw new \InvalidArgumentException('referenceId must be a valid UUID');
            }

            return;
        }

        $baseUrl = $this->outboundBaseUrl();
        $routePath = ltrim($this->config->getString('comml.route_path', '/api/route.php'), '/');
        $url = $baseUrl . '/' . $routePath;

        $body = json_encode([
            'contractId' => 'mdM.masterData.validate.v1',
            'sourceModule' => 'publicationManagement',
            'mode' => 'forwarding',
            'correlationId' => $correlationId,
            'request' => [
                'method' => 'POST',
                'body' => [
                    'referenceType' => $referenceType,
                    'referenceId' => $referenceId,
                    'correlationId' => $correlationId,
                ],
            ],
        ], JSON_THROW_ON_ERROR);

        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => implode("\r\n", [
                    'Content-Type: application/json',
                    'X-Correlation-Id: ' . $correlationId,
                    'X-Actor-Type: SERVICE_ACCOUNT',
                    'X-Actor-Id: 00000000-0000-4000-8000-000000000001',
                ]),
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
        if (!is_array($decoded) || ($decoded['success'] ?? false) !== true) {
            throw new \InvalidArgumentException('master data reference validation failed via commL');
        }

        $targetResponse = $decoded['data']['response'] ?? null;
        if (!is_array($targetResponse) || ($targetResponse['valid'] ?? false) !== true) {
            throw new \InvalidArgumentException('master data reference validation failed via commL');
        }
    }

    /**
     * Outbound commL URL. Defaults to comml.base_url; override via comml.outbound_base_url
     * or COMML_OUTBOUND_BASE_URL (required for PHP built-in server smoke — avoids inbound deadlock).
     */
    private function outboundBaseUrl(): string
    {
        $env = getenv('COMML_OUTBOUND_BASE_URL');
        if (is_string($env) && $env !== '') {
            return rtrim($env, '/');
        }

        $configured = $this->config->get('comml.outbound_base_url');
        if (is_string($configured) && $configured !== '') {
            return rtrim($configured, '/');
        }

        return rtrim($this->config->getString('comml.base_url'), '/');
    }
}
