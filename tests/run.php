<?php

declare(strict_types=1);

use PubM\Audit\PublicationAuditLogger;
use PubM\Audit\PublicationAuditRepository;
use PubM\Autoloader;
use PubM\Domain\Service\DraftService;
use PubM\Domain\Service\PublicationAuditService;
use PubM\Domain\Service\PublicationHistoryService;
use PubM\Domain\Service\PublicationService;
use PubM\Domain\Service\ScheduleService;
use PubM\Infrastructure\ActorContext;
use PubM\Infrastructure\Clock;
use PubM\Infrastructure\CommLGateway;
use PubM\Infrastructure\Config;
use PubM\Infrastructure\Correlation;
use PubM\Infrastructure\Database;
use PubM\Infrastructure\Uuid;
use PubM\Repository\PublicationChannelRepository;
use PubM\Repository\PublicationDraftRepository;
use PubM\Repository\PublicationRepository;
use PubM\Repository\PublicationScheduleRepository;
use PubM\Repository\PublicationTargetRepository;
use PubM\Repository\PublicationVersionRepository;

require dirname(__DIR__) . '/src-php/Autoloader.php';
Autoloader::register(dirname(__DIR__) . '/src-php');

$failures = 0;

$failures += test('Uuid v4 format', static fn (): bool => Uuid::isValid(Uuid::v4()));
$failures += test('Uuid rejects invalid', static fn (): bool => !Uuid::isValid('not-a-uuid'));

$context = build_test_context();
$failures += test('lifecycle draft to published', function () use ($context): bool {
    return run_lifecycle_test($context);
});
$failures += test('schedule cron processing', function () use ($context): bool {
    return run_schedule_test($context);
});
$failures += test('audit trail immutability', function () use ($context): bool {
    return run_audit_test($context);
});
$failures += test('persistence reload', function () use ($context): bool {
    return run_persistence_test($context);
});

exit($failures === 0 ? 0 : 1);

function test(string $name, callable $fn): int
{
    try {
        if (!$fn()) {
            fwrite(STDERR, "FAIL: {$name}\n");
            return 1;
        }
        fwrite(STDOUT, "PASS: {$name}\n");
        return 0;
    } catch (\Throwable $exception) {
        fwrite(STDERR, "FAIL: {$name} — {$exception->getMessage()}\n");
        return 1;
    }
}

/** @return array<string, mixed> */
function build_test_context(): array
{
    $rootDir = dirname(__DIR__);
    $useMariaDb = !extension_loaded('pdo_sqlite');

    if ($useMariaDb) {
        $configPath = $rootDir . '/config/config.php';
        if (!is_file($configPath)) {
            throw new RuntimeException('config/config.php required when pdo_sqlite unavailable');
        }
        $config = Config::load($configPath);
    } else {
        $config = new Config([
            'app' => ['timezone' => 'UTC'],
            'database' => ['driver' => 'sqlite', 'path' => ':memory:'],
            'comml' => ['enabled' => false],
        ]);
    }

    $database = new Database($config);
    if (!$useMariaDb) {
        $schema = file_get_contents($rootDir . '/tests/support/sqlite_schema.sql');
        if ($schema === false) {
            throw new RuntimeException('unable to load sqlite schema');
        }
        $database->pdo()->exec($schema);
    }

    $clock = new Clock();
    $auditRepository = new PublicationAuditRepository($database);
    $audit = new PublicationAuditLogger($auditRepository, $clock);
    $publications = new PublicationRepository($database);
    $drafts = new PublicationDraftRepository($database);
    $schedules = new PublicationScheduleRepository($database);
    $versions = new PublicationVersionRepository($database);
    $channels = new PublicationChannelRepository($database);
    $targets = new PublicationTargetRepository($database);
    $comml = new CommLGateway($config);

    $publicationService = new PublicationService($database, $publications, $drafts, $versions, $schedules, $targets, $audit, $clock);
    $draftService = new DraftService($database, $publications, $drafts, $channels, $targets, $audit, $comml, $clock);
    $scheduleService = new ScheduleService($database, $publications, $schedules, $publicationService, $audit, $clock);
    $historyService = new PublicationHistoryService($publications, $drafts, $versions, $schedules, $targets);
    $auditService = new PublicationAuditService($auditRepository);

    return [
        'draftService' => $draftService,
        'publicationService' => $publicationService,
        'scheduleService' => $scheduleService,
        'historyService' => $historyService,
        'auditService' => $auditService,
        'actor' => ActorContext::fromHeaders('USER', 'aaaaaaaa-aaaa-4aaa-8aaa-aaaaaaaaaaaa'),
        'correlationId' => Correlation::resolve(null),
    ];
}

/** @param array<string, mixed> $context */
function run_lifecycle_test(array $context): bool
{
    /** @var DraftService $draftService */
    $draftService = $context['draftService'];
    /** @var PublicationService $publicationService */
    $publicationService = $context['publicationService'];
    /** @var PublicationHistoryService $historyService */
    $historyService = $context['historyService'];
    $actor = $context['actor'];
    $correlationId = $context['correlationId'];

    $draft = $draftService->create([
        'title' => 'Test Publication',
        'content' => 'Lifecycle test content',
    ], $actor, $correlationId);

    $publicationId = (string) $draft['publicationId'];

    $updated = $draftService->update((string) $draft['draftId'], [
        'title' => 'Updated Title',
    ], $actor, $correlationId);

    if (($updated['titleSnapshot'] ?? '') !== 'Updated Title') {
        return false;
    }

    $review = $publicationService->submitForReview($publicationId, $actor, $correlationId);
    if (($review['currentStatus'] ?? '') !== 'REVIEW') {
        return false;
    }

    $approved = $publicationService->approve($publicationId, $actor, $correlationId);
    if (($approved['currentStatus'] ?? '') !== 'APPROVED') {
        return false;
    }

    $published = $publicationService->publish($publicationId, $actor, $correlationId);
    if (($published['currentStatus'] ?? '') !== 'PUBLISHED') {
        return false;
    }

    $history = $historyService->getHistory($publicationId);
    if (count($history['versions'] ?? []) < 1) {
        return false;
    }

    return true;
}

/** @param array<string, mixed> $context */
function run_schedule_test(array $context): bool
{
    /** @var DraftService $draftService */
    $draftService = $context['draftService'];
    /** @var PublicationService $publicationService */
    $publicationService = $context['publicationService'];
    /** @var ScheduleService $scheduleService */
    $scheduleService = $context['scheduleService'];
    $actor = $context['actor'];
    $correlationId = $context['correlationId'];

    $draft = $draftService->create([
        'title' => 'Scheduled Publication',
        'content' => 'Schedule test',
    ], $actor, $correlationId);

    $publicationId = (string) $draft['publicationId'];
    $publicationService->submitForReview($publicationId, $actor, $correlationId);
    $publicationService->approve($publicationId, $actor, $correlationId);

    $past = gmdate('Y-m-d H:i:s', time() - 60);
    $scheduleService->schedule($publicationId, [
        'scheduledAt' => $past,
        'maxRetries' => 2,
    ], $actor, $correlationId);

    $result = $scheduleService->processDueSchedules($actor);
    if (($result['succeeded'] ?? 0) < 1) {
        return false;
    }

    $publication = $publicationService->get($publicationId);
    return ($publication['currentStatus'] ?? '') === 'PUBLISHED';
}

/** @param array<string, mixed> $context */
function run_audit_test(array $context): bool
{
    /** @var DraftService $draftService */
    $draftService = $context['draftService'];
    /** @var PublicationService $publicationService */
    $publicationService = $context['publicationService'];
    /** @var PublicationAuditService $auditService */
    $auditService = $context['auditService'];
    $actor = $context['actor'];
    $correlationId = $context['correlationId'];

    $draft = $draftService->create([
        'title' => 'Audit Test',
        'content' => 'Audit content',
    ], $actor, $correlationId);

    $publicationId = (string) $draft['publicationId'];
    $publicationService->submitForReview($publicationId, $actor, $correlationId);
    $publicationService->approve($publicationId, $actor, $correlationId);

    $records = $auditService->listForPublication($publicationId);
    $actions = array_column($records, 'action');

    return in_array('DRAFT_CREATED', $actions, true)
        && in_array('REVIEW_SUBMITTED', $actions, true)
        && in_array('PUBLICATION_APPROVED', $actions, true);
}

/** @param array<string, mixed> $context */
function run_persistence_test(array $context): bool
{
    /** @var DraftService $draftService */
    $draftService = $context['draftService'];
    /** @var PublicationService $publicationService */
    $publicationService = $context['publicationService'];
    $actor = $context['actor'];
    $correlationId = $context['correlationId'];

    $draft = $draftService->create([
        'title' => 'Persistence Test',
        'content' => 'Persist me',
    ], $actor, $correlationId);

    $loaded = $draftService->get((string) $draft['draftId']);
    if (($loaded['titleSnapshot'] ?? '') !== 'Persistence Test') {
        return false;
    }

    $publication = $publicationService->get((string) $draft['publicationId']);
    return ($publication['currentStatus'] ?? '') === 'DRAFT';
}
