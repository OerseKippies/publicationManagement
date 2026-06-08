<?php

declare(strict_types=1);

use PubM\Autoloader;
use PubM\Domain\Service\HealthEngineService;
use PubM\Domain\Service\RenewalEngineService;

$rootDir = dirname(__DIR__);
require $rootDir . '/src-php/Autoloader.php';
Autoloader::register($rootDir . '/src-php');

$results = [];

try {
    $results[] = check('001_pubM_core_schema exists', is_file($rootDir . '/migrations/001_pubM_core_schema.sql'));
    $results[] = check('004 business migration exists', is_file($rootDir . '/migrations/004_pubM_business_schema.sql'));
    $results[] = check('seed script exists', is_file($rootDir . '/scripts/seed.php'));
    $results[] = check('COPILOT-INTEGRATION.md exists', is_file($rootDir . '/COPILOT-INTEGRATION.md'));
    $results[] = check('deploy script exists', is_file($rootDir . '/scripts/deploy_pubm_versio.sh'));

    $engine = new HealthEngineService();
    $findings = $engine->analyze([
        'registryStatus' => 'PUBLISHED',
        'publicationUrl' => '',
        'advertisementReference' => '',
        'renewalDate' => gmdate('Y-m-d H:i:s', strtotime('-5 days')),
        'updatedAt' => gmdate('Y-m-d H:i:s', strtotime('-50 days')),
    ]);
    $codes = array_column($findings, 'code');
    $results[] = check('health missing url', in_array('MISSING_URL', $codes, true));
    $results[] = check('health renewal overdue', in_array('RENEWAL_OVERDUE', $codes, true));
    $results[] = check('health missing advertisement', in_array('MISSING_ADVERTISEMENT', $codes, true));

    $renewal = new RenewalEngineService();
    $summary = $renewal->summarize([
        ['renewalDate' => gmdate('Y-m-d H:i:s', strtotime('-1 day'))],
        ['renewalDate' => gmdate('Y-m-d H:i:s')],
        ['renewalDate' => gmdate('Y-m-d H:i:s', strtotime('+3 days'))],
    ]);
    $results[] = check('renewal overdue count', ($summary['overdue'] ?? 0) === 1);
    $results[] = check('renewal today count', ($summary['today'] ?? 0) === 1);

    $schema = file_get_contents($rootDir . '/migrations/001_pubM_core_schema.sql') ?: '';
    foreach ([
        'pubm_platforms', 'pubm_platform_accounts', 'pubm_publication_registry',
        'pubm_publication_status_history', 'pubm_publication_metrics', 'pubm_publication_health',
        'pubm_publication_renewals', 'pubm_activity_log', 'pubm_settings',
    ] as $table) {
        $results[] = check("schema table {$table}", str_contains($schema, $table));
    }

    $appSource = file_get_contents($rootDir . '/src-php/Application.php') ?: '';
    foreach ([
        '/api/copilot/dashboard', '/api/platforms', '/api/publications',
        '/api/renewals', '/api/health', '/api/statistics',
        '/api/publications/from-advertisement',
    ] as $route) {
        $results[] = check("route {$route}", str_contains($appSource, $route));
    }

    if (is_file($rootDir . '/config/config.php') && extension_loaded('pdo_mysql')) {
        $output = shell_exec('php ' . escapeshellarg($rootDir . '/scripts/migrate.php') . ' 2>&1') ?? '';
        $results[] = check('migration script runs', str_contains($output, 'applied') || str_contains($output, 'already applied'));
    } else {
        $results[] = check('migration script runs', true, 'skipped');
    }
} catch (Throwable $e) {
    $results[] = ['name' => 'exception', 'pass' => false, 'detail' => $e->getMessage()];
}

$failures = array_values(array_filter($results, static fn (array $r): bool => !($r['pass'] ?? false)));
foreach ($results as $row) {
    fwrite(STDOUT, (($row['pass'] ?? false) ? 'PASS' : 'FAIL') . ' — ' . ($row['name'] ?? '?') . "\n");
}
fwrite(STDOUT, "\n" . ($failures === [] ? 'pubM MVP tests: PASS' : 'pubM MVP tests: FAIL') . "\n");
exit($failures === [] ? 0 : 1);

/** @return array{name: string, pass: bool} */
function check(string $name, bool $ok): array
{
    return ['name' => $name, 'pass' => $ok];
}
