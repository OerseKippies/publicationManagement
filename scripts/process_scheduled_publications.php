<?php

declare(strict_types=1);

use PubM\Autoloader;
use PubM\Domain\Service\ScheduleService;
use PubM\Infrastructure\Clock;
use PubM\Infrastructure\Config;
use PubM\Infrastructure\Database;
use PubM\Audit\PublicationAuditLogger;
use PubM\Audit\PublicationAuditRepository;
use PubM\Domain\Service\PublicationService;
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
$audit = new PublicationAuditLogger(new PublicationAuditRepository($database), $clock);
$publications = new PublicationRepository($database);
$drafts = new PublicationDraftRepository($database);
$schedules = new PublicationScheduleRepository($database);
$versions = new PublicationVersionRepository($database);
$targets = new PublicationTargetRepository($database);

$publicationService = new PublicationService($database, $publications, $drafts, $versions, $schedules, $targets, $audit, $clock);
$scheduleService = new ScheduleService($database, $publications, $schedules, $publicationService, $audit, $clock);

$result = $scheduleService->processDueSchedules();

fwrite(STDOUT, json_encode($result, JSON_PRETTY_PRINT) . PHP_EOL);
exit(0);
