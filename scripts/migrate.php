<?php

declare(strict_types=1);

use PubM\Autoloader;
use PubM\Infrastructure\Config;
use PubM\Infrastructure\Database;

$rootDir = dirname(__DIR__);

require $rootDir . '/src-php/Autoloader.php';
Autoloader::register($rootDir . '/src-php');

$configPath = $rootDir . '/config/config.php';
if (!is_file($configPath)) {
    fwrite(STDERR, "Missing config/config.php. Copy config.example.php first.\n");
    exit(1);
}

$config = Config::load($configPath);
$database = new Database($config);
$pdo = $database->pdo();

$migrations = [
    '001_publications' => ['dir' => 'schemas', 'file' => '001_publications.sql'],
    '004_pubM_business_schema' => ['dir' => 'migrations', 'file' => '004_pubM_business_schema.sql'],
];

foreach ($migrations as $migrationId => $meta) {
    try {
        $statement = $pdo->prepare('SELECT migrationId FROM pubm_schema_migrations WHERE migrationId = :migrationId LIMIT 1');
        $statement->execute(['migrationId' => $migrationId]);
        if ($statement->fetch() !== false) {
            fwrite(STDOUT, "Migration {$migrationId} already applied.\n");
            continue;
        }
    } catch (\PDOException) {
        // Table may not exist yet on first migration.
    }

    $path = $rootDir . '/' . $meta['dir'] . '/' . $meta['file'];
    $sql = file_get_contents($path);
    if ($sql === false) {
        fwrite(STDERR, "Unable to read migration SQL: {$meta['file']}\n");
        exit(1);
    }

    $pdo->exec($sql);

    $insert = $pdo->prepare('INSERT INTO pubm_schema_migrations (migrationId, appliedAt) VALUES (:migrationId, :appliedAt)');
    $insert->execute([
        'migrationId' => $migrationId,
        'appliedAt' => gmdate('Y-m-d H:i:s'),
    ]);

    fwrite(STDOUT, "Migration {$migrationId} applied.\n");
}
