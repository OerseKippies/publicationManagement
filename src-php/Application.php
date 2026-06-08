<?php

declare(strict_types=1);

namespace PubM;

use PubM\Audit\PublicationAuditLogger;
use PubM\Audit\PublicationAuditRepository;
use PubM\Domain\Service\BusinessWorkspaceService;
use PubM\Domain\Service\DraftService;
use PubM\Domain\Service\HealthEngineService;
use PubM\Domain\Service\RenewalEngineService;
use PubM\Domain\Service\PublicationAuditService;
use PubM\Domain\Service\PublicationHistoryService;
use PubM\Domain\Service\PublicationService;
use PubM\Domain\Service\ScheduleService;
use PubM\Http\ApiException;
use PubM\Http\Request;
use PubM\Http\Response;
use PubM\Http\Router;
use PubM\Infrastructure\ActorContext;
use PubM\Infrastructure\Clock;
use PubM\Infrastructure\CommLGateway;
use PubM\Infrastructure\Config;
use PubM\Infrastructure\Database;
use PubM\Repository\BusinessRepository;
use PubM\Repository\PublicationChannelRepository;
use PubM\Repository\PublicationDraftRepository;
use PubM\Repository\PublicationRepository;
use PubM\Repository\PublicationScheduleRepository;
use PubM\Repository\PublicationTargetRepository;
use PubM\Repository\PublicationVersionRepository;

final class Application
{
    private Config $config;
    private Database $database;
    private Clock $clock;
    private Router $router;

    public function __construct(string $configPath)
    {
        $this->config = Config::load($configPath);
        date_default_timezone_set($this->config->getString('app.timezone', 'UTC'));

        $this->database = new Database($this->config);
        $this->clock = new Clock();
        $this->router = $this->buildRouter();
    }

    public function handle(Request $request): Response
    {
        try {
            if (!$this->isHealthRequest($request) && $this->config->getBool('api.require_api_key', false)) {
                $provided = $request->header('x-api-key');
                $expected = $this->config->getString('api.api_key');
                if ($provided === null || !hash_equals($expected, $provided)) {
                    return Response::error('FORBIDDEN', 'invalid or missing API key', 403, $request->correlationId, $this->clock);
                }
            }

            return $this->router->dispatch($request);
        } catch (ApiException $exception) {
            return Response::error($exception->errorCode, $exception->getMessage(), $exception->statusCode, $request->correlationId, $this->clock);
        } catch (\Throwable) {
            return Response::error('INTERNAL_ERROR', 'unexpected server error', 500, $request->correlationId, $this->clock);
        }
    }

    private function isHealthRequest(Request $request): bool
    {
        return $request->method === 'GET' && in_array($request->path, ['/health', '/v1/health'], true);
    }

    private function buildRouter(): Router
    {
        $auditRepository = new PublicationAuditRepository($this->database);
        $auditLogger = new PublicationAuditLogger($auditRepository, $this->clock);

        $publications = new PublicationRepository($this->database);
        $drafts = new PublicationDraftRepository($this->database);
        $schedules = new PublicationScheduleRepository($this->database);
        $versions = new PublicationVersionRepository($this->database);
        $channels = new PublicationChannelRepository($this->database);
        $targets = new PublicationTargetRepository($this->database);
        $comml = new CommLGateway($this->config);

        $publicationService = new PublicationService(
            $this->database,
            $publications,
            $drafts,
            $versions,
            $schedules,
            $targets,
            $auditLogger,
            $this->clock
        );

        $draftService = new DraftService(
            $this->database,
            $publications,
            $drafts,
            $channels,
            $targets,
            $auditLogger,
            $comml,
            $this->clock
        );

        $scheduleService = new ScheduleService(
            $this->database,
            $publications,
            $schedules,
            $publicationService,
            $auditLogger,
            $this->clock
        );

        $historyService = new PublicationHistoryService($publications, $drafts, $versions, $schedules, $targets);
        $auditService = new PublicationAuditService($auditRepository);

        $businessRepo = new BusinessRepository($this->database);
        $businessWorkspace = new BusinessWorkspaceService(
            $this->database,
            $businessRepo,
            new HealthEngineService(),
            new RenewalEngineService(),
            $this->clock
        );

        $router = new Router();
        $actor = static fn (Request $request): ActorContext => ActorContext::fromHeaders(
            $request->header('x-actor-type'),
            $request->header('x-actor-id')
        );

        $router->add('GET', '/health', fn (Request $request): Response => $this->healthResponse($request));
        $router->add('GET', '/v1/health', fn (Request $request): Response => $this->healthResponse($request));

        $router->add('POST', '/publications/drafts', function (Request $request) use ($draftService, $actor): Response {
            $payload = $request->body ?? [];
            $draft = $draftService->create($payload, $actor($request), $request->correlationId);

            return Response::json(201, ['draft' => $draft], $request->correlationId);
        });

        $router->add('POST', '/api/v1/publications', function (Request $request) use ($draftService, $actor): Response {
            $payload = $request->body ?? [];
            $draft = $draftService->create($payload, $actor($request), $request->correlationId);

            return Response::json(201, ['draft' => $draft], $request->correlationId);
        });

        $router->add('POST', '/api/v1/publications/from-inventory', function (Request $request) use ($draftService, $actor): Response {
            $payload = $request->body ?? [];
            $draft = $draftService->createFromInventory($payload, $actor($request), $request->correlationId);

            return Response::json(201, ['draft' => $draft, 'publication' => $draft['publication'] ?? null], $request->correlationId);
        });

        $router->add('GET', '/api/v1/publications', function (Request $request) use ($publications): Response {
            $sourceModule = isset($request->query['sourceModule']) ? (string) $request->query['sourceModule'] : null;
            $sourceObjectId = isset($request->query['sourceObjectId']) ? (string) $request->query['sourceObjectId'] : null;
            $limit = isset($request->query['limit']) ? (int) $request->query['limit'] : 100;

            return Response::json(200, [
                'publications' => $publications->list($sourceModule, $sourceObjectId, $limit),
            ], $request->correlationId);
        });

        $router->add('GET', '/publications', function (Request $request) use ($publications): Response {
            $sourceModule = isset($request->query['sourceModule']) ? (string) $request->query['sourceModule'] : null;
            $sourceObjectId = isset($request->query['sourceObjectId']) ? (string) $request->query['sourceObjectId'] : null;
            $limit = isset($request->query['limit']) ? (int) $request->query['limit'] : 100;

            return Response::json(200, [
                'publications' => $publications->list($sourceModule, $sourceObjectId, $limit),
            ], $request->correlationId);
        });

        $router->add('GET', '/publications/drafts/(?P<id>[0-9a-f-]{36})', function (Request $request, array $params) use ($draftService): Response {
            return Response::json(200, ['draft' => $draftService->get($params['id'])], $request->correlationId);
        });

        $router->add('PUT', '/publications/drafts/(?P<id>[0-9a-f-]{36})', function (Request $request, array $params) use ($draftService, $actor): Response {
            $payload = $request->body ?? [];
            $draft = $draftService->update($params['id'], $payload, $actor($request), $request->correlationId);

            return Response::json(200, ['draft' => $draft], $request->correlationId);
        });

        $router->add('POST', '/publications/(?P<id>[0-9a-f-]{36})/submit-review', function (Request $request, array $params) use ($publicationService, $actor): Response {
            $publication = $publicationService->submitForReview($params['id'], $actor($request), $request->correlationId);

            return Response::json(200, ['publication' => $publication], $request->correlationId);
        });

        $router->add('POST', '/publications/(?P<id>[0-9a-f-]{36})/approve', function (Request $request, array $params) use ($publicationService, $actor): Response {
            $publication = $publicationService->approve($params['id'], $actor($request), $request->correlationId);

            return Response::json(200, ['publication' => $publication], $request->correlationId);
        });

        $router->add('POST', '/publications/(?P<id>[0-9a-f-]{36})/schedule', function (Request $request, array $params) use ($scheduleService, $actor): Response {
            $payload = $request->body ?? [];
            $result = $scheduleService->schedule($params['id'], $payload, $actor($request), $request->correlationId);

            return Response::json(200, $result, $request->correlationId);
        });

        $router->add('POST', '/publications/(?P<id>[0-9a-f-]{36})/publish', function (Request $request, array $params) use ($publicationService, $actor): Response {
            $publication = $publicationService->publish($params['id'], $actor($request), $request->correlationId);

            return Response::json(200, ['publication' => $publication], $request->correlationId);
        });

        $router->add('GET', '/publications/(?P<id>[0-9a-f-]{36})/history', function (Request $request, array $params) use ($historyService): Response {
            return Response::json(200, $historyService->getHistory($params['id']), $request->correlationId);
        });

        $router->add('GET', '/publications/(?P<id>[0-9a-f-]{36})/audit', function (Request $request, array $params) use ($auditService): Response {
            $limit = isset($request->query['limit']) ? (int) $request->query['limit'] : 100;

            return Response::json(200, [
                'auditRecords' => $auditService->listForPublication($params['id'], $limit),
            ], $request->correlationId);
        });

        $router->add('GET', '/publications/(?P<id>[0-9a-f-]{36})', function (Request $request, array $params) use ($publicationService): Response {
            return Response::json(200, ['publication' => $publicationService->get($params['id'])], $request->correlationId);
        });

        // Sprint business API
        $router->add('GET', '/api/platforms', fn (Request $request): Response => Response::json(
            200,
            ['items' => $businessWorkspace->listPlatforms()],
            $request->correlationId
        ));
        $router->add('GET', '/api/publications', fn (Request $request): Response => Response::json(
            200,
            ['items' => $businessWorkspace->listRegistry(is_string($_GET['advertisementReference'] ?? null) ? $_GET['advertisementReference'] : null)],
            $request->correlationId
        ));
        $router->add('POST', '/api/publications/from-advertisement', fn (Request $request): Response => Response::json(
            201,
            $businessWorkspace->createFromAdvertisement($request->body ?? [], $actor($request)),
            $request->correlationId
        ));
        $router->add('POST', '/api/publications/(?P<registryId>[0-9a-f-]{36})/publish', fn (Request $request, array $params): Response => Response::json(
            200,
            $businessWorkspace->transition($params['registryId'], 'PUBLISHED', $actor($request), 'Published'),
            $request->correlationId
        ));
        $router->add('POST', '/api/publications/(?P<registryId>[0-9a-f-]{36})/archive', fn (Request $request, array $params): Response => Response::json(
            200,
            $businessWorkspace->transition($params['registryId'], 'ARCHIVED', $actor($request), 'Archived'),
            $request->correlationId
        ));
        $router->add('POST', '/api/publications/(?P<registryId>[0-9a-f-]{36})/expire', fn (Request $request, array $params): Response => Response::json(
            200,
            $businessWorkspace->transition($params['registryId'], 'EXPIRED', $actor($request), 'Expired'),
            $request->correlationId
        ));
        $router->add('POST', '/api/publications/(?P<registryId>[0-9a-f-]{36})/remove', fn (Request $request, array $params): Response => Response::json(
            200,
            $businessWorkspace->transition($params['registryId'], 'REMOVED', $actor($request), 'Removed'),
            $request->correlationId
        ));
        $router->add('POST', '/api/publications/(?P<registryId>[0-9a-f-]{36})/renew', fn (Request $request, array $params): Response => Response::json(
            201,
            $businessWorkspace->renew($params['registryId'], $request->body ?? [], $actor($request)),
            $request->correlationId
        ));
        $router->add('POST', '/api/publications/(?P<registryId>[0-9a-f-]{36})/metrics', fn (Request $request, array $params): Response => Response::json(
            201,
            $businessWorkspace->saveMetrics($params['registryId'], $request->body ?? [], $actor($request)),
            $request->correlationId
        ));
        $router->add('GET', '/api/renewals', fn (Request $request): Response => Response::json(
            200,
            ['items' => $businessWorkspace->listRenewals()],
            $request->correlationId
        ));
        $router->add('GET', '/api/health', fn (Request $request): Response => Response::json(
            200,
            ['items' => $businessWorkspace->listHealth()],
            $request->correlationId
        ));
        $router->add('GET', '/api/statistics', fn (Request $request): Response => Response::json(
            200,
            $businessWorkspace->statisticsSummary(),
            $request->correlationId
        ));
        $router->add('GET', '/api/copilot/dashboard', fn (Request $request): Response => Response::json(
            200,
            $businessWorkspace->copilotDashboard(),
            $request->correlationId
        ));

        return $router;
    }

    private function healthResponse(Request $request): Response
    {
        $dbStatus = 'error';
        $dbName = null;
        $tableCount = null;
        try {
            $pdo = $this->database->pdo();
            $pdo->query('SELECT 1');
            $dbStatus = 'connected';
            $dbName = (string) ($pdo->query('SELECT DATABASE()')->fetchColumn() ?: '');
            $row = $pdo->query(
                "SELECT COUNT(*) AS c FROM information_schema.tables WHERE table_schema = DATABASE()"
            )->fetch();
            $tableCount = (int) ($row['c'] ?? 0);
        } catch (\Throwable) {
            $dbStatus = 'error';
        }

        return Response::json(200, [
            'status' => $dbStatus === 'connected' ? 'healthy' : 'degraded',
            'module' => 'publicationManagement',
            'moduleCode' => 'pubM',
            'database' => $dbStatus,
            'database_name' => $dbName,
            'table_count' => $tableCount,
            'timestamp' => $this->clock->nowIso8601(),
        ], $request->correlationId);
    }
}
