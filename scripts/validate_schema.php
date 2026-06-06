<?php

declare(strict_types=1);

use PubM\Autoloader;
use PubM\Infrastructure\Config;
use PubM\Infrastructure\Database;

require dirname(__DIR__) . '/src-php/Autoloader.php';
Autoloader::register(dirname(__DIR__) . '/src-php');

$config = Config::load(dirname(__DIR__) . '/config/config.php');
$database = new Database($config);
$pdo = $database->pdo();

$tables = [
    'pubm_schema_migrations', 'publications', 'publication_drafts', 'publication_templates',
    'publication_channels', 'publication_targets', 'publication_schedules',
    'publication_versions', 'publication_audit_records',
];

$result = ['tables' => [], 'indexes' => [], 'foreign_keys' => []];

foreach ($tables as $table) {
    $stmt = $pdo->query("SHOW TABLES LIKE '{$table}'");
    $result['tables'][$table] = $stmt !== false && $stmt->fetch() !== false;
}

foreach (['publications', 'publication_drafts', 'publication_schedules', 'publication_audit_records'] as $table) {
    $indexes = $pdo->query("SHOW INDEX FROM {$table}")->fetchAll();
    $result['indexes'][$table] = array_map(static fn (array $row): string => (string) $row['Key_name'], $indexes);
}

$fkQuery = $pdo->query(
    "SELECT TABLE_NAME, CONSTRAINT_NAME, REFERENCED_TABLE_NAME
     FROM information_schema.KEY_COLUMN_USAGE
     WHERE TABLE_SCHEMA = DATABASE()
       AND REFERENCED_TABLE_NAME IS NOT NULL
       AND TABLE_NAME LIKE 'publication%'"
);
$result['foreign_keys'] = $fkQuery !== false ? $fkQuery->fetchAll() : [];

echo json_encode($result, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR) . PHP_EOL;
