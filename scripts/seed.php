<?php

declare(strict_types=1);

use PubM\Autoloader;
use PubM\Domain\Service\BusinessWorkspaceService;
use PubM\Domain\Service\HealthEngineService;
use PubM\Domain\Service\RenewalEngineService;
use PubM\Infrastructure\ActorContext;
use PubM\Infrastructure\Clock;
use PubM\Infrastructure\Config;
use PubM\Infrastructure\Database;
use PubM\Infrastructure\Uuid;
use PubM\Repository\BusinessRepository;

$rootDir = dirname(__DIR__);
require $rootDir . '/src-php/Autoloader.php';
Autoloader::register($rootDir . '/src-php');

$config = Config::load($rootDir . '/config/config.php');
$database = new Database($config);
$pdo = $database->pdo();
$clock = new Clock();
$now = $clock->nowUtc();
$actor = new ActorContext('SYSTEM', null);

$platforms = [
    ['slug' => 'marktplaats', 'name' => 'Marktplaats', 'mode' => 'MANUAL'],
    ['slug' => '2dehands', 'name' => '2dehands', 'mode' => 'MANUAL'],
    ['slug' => 'facebook-marketplace', 'name' => 'Facebook Marketplace', 'mode' => 'MANUAL'],
    ['slug' => 'website', 'name' => 'Website', 'mode' => 'MANUAL'],
    ['slug' => 'other', 'name' => 'Other', 'mode' => 'MANUAL'],
];

$platformIds = [];
foreach ($platforms as $platform) {
    $existing = $pdo->prepare('SELECT platformId FROM pubm_platforms WHERE slug = :slug LIMIT 1');
    $existing->execute(['slug' => $platform['slug']]);
    $row = $existing->fetch();
    if ($row !== false) {
        $platformIds[$platform['slug']] = (string) $row['platformId'];
        continue;
    }
    $id = Uuid::v4();
    $pdo->prepare(
        'INSERT INTO pubm_platforms (platformId, name, slug, status, mode, notes, createdAt, updatedAt)
         VALUES (:platformId, :name, :slug, :status, :mode, :notes, :createdAt, :updatedAt)'
    )->execute([
        'platformId' => $id,
        'name' => $platform['name'],
        'slug' => $platform['slug'],
        'status' => 'ACTIVE',
        'mode' => $platform['mode'],
        'notes' => 'Oerse Kippies platform registry',
        'createdAt' => $now,
        'updatedAt' => $now,
    ]);
    $platformIds[$platform['slug']] = $id;
    fwrite(STDOUT, "Seeded platform {$platform['name']}.\n");
}

$business = new BusinessWorkspaceService(
    $database,
    new BusinessRepository($database),
    new HealthEngineService(),
    new RenewalEngineService(),
    $clock
);

$seedPublications = [
    ['title' => 'Cream Legbar', 'platform' => 'marktplaats', 'url' => 'https://marktplaats.nl/v/cream-legbar-oerse-kippies'],
    ['title' => 'Marans', 'platform' => 'marktplaats', 'url' => 'https://marktplaats.nl/v/marans-oerse-kippies'],
    ['title' => 'Faverolles', 'platform' => 'marktplaats', 'url' => 'https://marktplaats.nl/v/faverolles-oerse-kippies'],
    ['title' => 'Kuikenstartpakket', 'platform' => '2dehands', 'url' => 'https://2dehands.be/v/kuikenstartpakket'],
    ['title' => 'VitalBoost', 'platform' => '2dehands', 'url' => 'https://2dehands.be/v/vitalboost'],
    ['title' => 'Maagkiezel', 'platform' => '2dehands', 'url' => 'https://2dehands.be/v/maagkiezel'],
    ['title' => 'Wormenmix', 'platform' => '2dehands', 'url' => 'https://2dehands.be/v/wormenmix'],
];

foreach ($seedPublications as $item) {
    $adRef = Uuid::v4();
    $registry = $business->createFromAdvertisement([
        'advertisementReference' => $adRef,
        'platformId' => $platformIds[$item['platform']],
        'publicationUrl' => $item['url'],
        'externalIdentifier' => 'ok-' . substr($adRef, 0, 8),
        'notes' => $item['title'] . ' — Oerse Kippies seed publication',
        'renewalDate' => gmdate('Y-m-d H:i:s', strtotime('+21 days')),
    ], $actor);

    $registryId = (string) $registry['registryId'];
    $business->transition($registryId, 'PUBLISHED', $actor, 'Seed publish');
    $business->saveMetrics($registryId, [
        'views' => random_int(30, 150),
        'messages' => random_int(3, 20),
        'favorites' => random_int(1, 10),
        'leads' => random_int(1, 8),
        'conversions' => random_int(0, 4),
        'sales' => random_int(0, 3),
    ], $actor);
    $business->refreshHealth($registryId);
    fwrite(STDOUT, "Seeded publication {$item['title']} on {$item['platform']}.\n");
}

fwrite(STDOUT, "Seed complete.\n");
