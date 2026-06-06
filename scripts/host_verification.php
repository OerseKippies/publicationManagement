<?php

declare(strict_types=1);

/**
 * Host verification — full lifecycle + API + scheduling against configured MariaDB.
 * Usage: php scripts/host_verification.php
 */

use PubM\Application;
use PubM\Http\Request;
use PubM\Infrastructure\Correlation;
use PubM\Infrastructure\Config;
use PubM\Infrastructure\Database;

require dirname(__DIR__) . '/src-php/Autoloader.php';
PubM\Autoloader::register(dirname(__DIR__) . '/src-php');

$rootDir = dirname(__DIR__);
$configPath = $rootDir . '/config/config.php';
if (!is_file($configPath)) {
    fwrite(STDERR, "Missing config/config.php\n");
    exit(1);
}

$config = Config::load($configPath);
$database = new Database($config);
$application = new Application($configPath);

$correlationId = Correlation::resolve(null);
$actorHeaders = [
    'x-actor-type' => 'SERVICE_ACCOUNT',
    'x-actor-id' => '00000000-0000-4000-8000-000000000001',
    'content-type' => 'application/json',
];

$results = [];
$publicationId = '';
$draftId = '';

function api(Application $app, string $method, string $path, ?array $body, string $correlationId, array $headers): array
{
    $request = new Request($method, $path, $headers, $body, $correlationId);
    $response = $app->handle($request);
    $decoded = is_array($response->body) ? $response->body : null;

    return [
        'statusCode' => $response->statusCode,
        'body' => $decoded,
    ];
}

$results['php_version'] = PHP_VERSION;

// 1. Health
$health = api($application, 'GET', '/health', null, $correlationId, $actorHeaders);
$results['health'] = $health;

// 2. Create draft
$create = api($application, 'POST', '/publications/drafts', [
    'title' => 'Host Verification Publication',
    'content' => 'Host verification lifecycle content',
    'sourceModule' => 'invM',
    'sourceObjectId' => '11111111-1111-4111-8111-111111111111',
], $correlationId, $actorHeaders);
$results['create_draft'] = $create;
$draftId = (string) ($create['body']['draft']['draftId'] ?? '');
$publicationId = (string) ($create['body']['draft']['publicationId'] ?? '');

// 3. Get draft
$results['get_draft'] = api($application, 'GET', '/publications/drafts/' . $draftId, null, $correlationId, $actorHeaders);

// 4. Update draft
$results['update_draft'] = api($application, 'PUT', '/publications/drafts/' . $draftId, [
    'title' => 'Host Verification Updated',
], $correlationId, $actorHeaders);

// 5. Submit review
$results['submit_review'] = api($application, 'POST', '/publications/' . $publicationId . '/submit-review', [], $correlationId, $actorHeaders);

// 6. Approve
$results['approve'] = api($application, 'POST', '/publications/' . $publicationId . '/approve', [], $correlationId, $actorHeaders);

// 7. Schedule (past time for cron)
$past = gmdate('Y-m-d H:i:s', time() - 120);
$results['schedule'] = api($application, 'POST', '/publications/' . $publicationId . '/schedule', [
    'scheduledAt' => $past,
    'maxRetries' => 3,
], $correlationId, $actorHeaders);

// 8. Cron processor (inline)
use PubM\Audit\PublicationAuditLogger;
use PubM\Audit\PublicationAuditRepository;
use PubM\Domain\Service\PublicationService;
use PubM\Domain\Service\ScheduleService;
use PubM\Infrastructure\ActorContext;
use PubM\Infrastructure\Clock;
use PubM\Repository\PublicationDraftRepository;
use PubM\Repository\PublicationRepository;
use PubM\Repository\PublicationScheduleRepository;
use PubM\Repository\PublicationTargetRepository;
use PubM\Repository\PublicationVersionRepository;

$clock = new Clock();
$audit = new PublicationAuditLogger(new PublicationAuditRepository($database), $clock);
$pubRepo = new PublicationRepository($database);
$draftRepo = new PublicationDraftRepository($database);
$schedRepo = new PublicationScheduleRepository($database);
$verRepo = new PublicationVersionRepository($database);
$targetRepo = new PublicationTargetRepository($database);
$pubSvc = new PublicationService($database, $pubRepo, $draftRepo, $verRepo, $schedRepo, $targetRepo, $audit, $clock);
$schedSvc = new ScheduleService($database, $pubRepo, $schedRepo, $pubSvc, $audit, $clock);
$results['cron_process'] = $schedSvc->processDueSchedules(ActorContext::fromHeaders('SYSTEM', null));

// 9. Get publication
$results['get_publication'] = api($application, 'GET', '/publications/' . $publicationId, null, $correlationId, $actorHeaders);

// 10. History
$results['history'] = api($application, 'GET', '/publications/' . $publicationId . '/history', null, $correlationId, $actorHeaders);

// 11. Audit
$results['audit'] = api($application, 'GET', '/publications/' . $publicationId . '/audit', null, $correlationId, $actorHeaders);

// Schema check
$tables = [
    'pubm_schema_migrations', 'publications', 'publication_drafts', 'publication_templates',
    'publication_channels', 'publication_targets', 'publication_schedules',
    'publication_versions', 'publication_audit_records',
];
$schemaCheck = [];
foreach ($tables as $table) {
    $stmt = $database->pdo()->query("SHOW TABLES LIKE '{$table}'");
    $schemaCheck[$table] = $stmt !== false && $stmt->fetch() !== false;
}
$results['schema_tables'] = $schemaCheck;

$published = ($results['get_publication']['body']['publication']['currentStatus'] ?? '') === 'PUBLISHED';
$auditCount = count($results['audit']['body']['auditRecords'] ?? []);
$results['summary'] = [
    'publicationId' => $publicationId,
    'finalStatus' => $results['get_publication']['body']['publication']['currentStatus'] ?? null,
    'auditRecordCount' => $auditCount,
    'allTablesPresent' => !in_array(false, $schemaCheck, true),
    'pass' => $published && $auditCount >= 5 && !in_array(false, $schemaCheck, true),
];

echo json_encode($results, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR) . PHP_EOL;
exit($results['summary']['pass'] ? 0 : 1);
