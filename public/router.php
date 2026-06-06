<?php

declare(strict_types=1);

if (php_sapi_name() === 'cli-server') {
    $uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/');
    $file = __DIR__ . $uri;
    if ($uri !== '/' && is_file($file)) {
        return false;
    }
}

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';
if ($path === '/health' || str_starts_with($path, '/api/v1/publications') || str_starts_with($path, '/publications')) {
    require __DIR__ . '/api/publications/index.php';
    return;
}

require __DIR__ . '/api/health.php';
