<?php

declare(strict_types=1);

use PubM\Autoloader;
use PubM\Domain\Service\DraftService;
use PubM\Domain\Service\PublicationService;
use PubM\Domain\Service\ScheduleService;
use PubM\Infrastructure\ActorContext;
use PubM\Infrastructure\Clock;
use PubM\Infrastructure\CommLGateway;
use PubM\Infrastructure\Config;
use PubM\Infrastructure\Correlation;
use PubM\Infrastructure\Database;
use PubM\Audit\PublicationAuditLogger;
use PubM\Audit\PublicationAuditRepository;
use PubM\Repository\PublicationChannelRepository;
use PubM\Repository\PublicationDraftRepository;
use PubM\Repository\PublicationRepository;
use PubM\Repository\PublicationScheduleRepository;
use PubM\Repository\PublicationTargetRepository;
use PubM\Repository\PublicationVersionRepository;

$rootDir = dirname(__DIR__);
require $rootDir . '/src-php/Autoloader.php';
Autoloader::register($rootDir . '/src-php');

$configPath = $rootDir . '/config/config.php';
if (!is_file($configPath)) {
    fwrite(STDERR, "Missing config/config.php — copy config.example.php\n");
    exit(1);
}

$config = Config::load($configPath);
$database = new Database($config);
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

$actor = ActorContext::fromHeaders('SERVICE_ACCOUNT', '00000000-0000-4000-8000-000000000001');
$correlationId = Correlation::resolve(null);
$publicationId = '';
$failures = 0;

$failures += assert_step('create draft', function () use ($draftService, $actor, $correlationId, &$publicationId): void {
    $draft = $draftService->create([
        'title' => 'Runtime Verify Publication',
        'content' => 'Verification content body',
        'sourceModule' => 'invM',
        'sourceObjectId' => '11111111-1111-4111-8111-111111111111',
    ], $actor, $correlationId);

    if (($draft['publication']['currentStatus'] ?? '') !== 'DRAFT') {
        throw new RuntimeException('expected DRAFT status');
    }

    $publicationId = (string) $draft['publicationId'];
});

$failures += assert_step('submit review', function () use ($publicationService, $actor, $correlationId, &$publicationId): void {
    $publication = $publicationService->submitForReview($publicationId, $actor, $correlationId);
    if (($publication['currentStatus'] ?? '') !== 'REVIEW') {
        throw new RuntimeException('expected REVIEW status');
    }
});

$failures += assert_step('approve publication', function () use ($publicationService, $actor, $correlationId, &$publicationId): void {
    $publication = $publicationService->approve($publicationId, $actor, $correlationId);
    if (($publication['currentStatus'] ?? '') !== 'APPROVED') {
        throw new RuntimeException('expected APPROVED status');
    }
});

$failures += assert_step('publish publication', function () use ($publicationService, $actor, $correlationId, &$publicationId): void {
    $publication = $publicationService->publish($publicationId, $actor, $correlationId);
    if (($publication['currentStatus'] ?? '') !== 'PUBLISHED') {
        throw new RuntimeException('expected PUBLISHED status');
    }
});

$failures += assert_step('audit trail present', function () use ($auditRepository, &$publicationId): void {
    $records = $auditRepository->findByPublication($publicationId);
    if (count($records) < 4) {
        throw new RuntimeException('expected at least 4 audit records');
    }
});

fwrite(STDOUT, $failures === 0 ? "Runtime verification PASSED\n" : "Runtime verification FAILED ({$failures})\n");
exit($failures === 0 ? 0 : 1);

function assert_step(string $name, callable $fn): int
{
    try {
        $fn();
        fwrite(STDOUT, "PASS: {$name}\n");
        return 0;
    } catch (\Throwable $exception) {
        fwrite(STDERR, "FAIL: {$name} — {$exception->getMessage()}\n");
        return 1;
    }
}
