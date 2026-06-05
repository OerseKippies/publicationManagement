<?php

declare(strict_types=1);

use PubM\Application;
use PubM\Autoloader;
use PubM\Http\Request;
use PubM\Infrastructure\Correlation;

function pubm_root_dir(): string
{
    return dirname(__DIR__);
}

function pubm_bootstrap(): void
{
    $rootDir = pubm_root_dir();
    require_once $rootDir . '/src-php/Autoloader.php';
    Autoloader::register($rootDir . '/src-php');
}

function pubm_config_path(): string
{
    $rootDir = pubm_root_dir();
    $configPath = $rootDir . '/config/config.php';

    return is_file($configPath) ? $configPath : $rootDir . '/config/config.example.php';
}

function pubm_handle_request(?string $pathOverride = null): void
{
    pubm_bootstrap();

    $correlationId = Correlation::resolve($_SERVER['HTTP_X_CORRELATION_ID'] ?? null);
    $request = pubm_normalize_path(Request::fromGlobals($correlationId), $pathOverride);

    $application = new Application(pubm_config_path());
    $application->handle($request)->send();
}

function pubm_normalize_path(Request $request, ?string $pathOverride = null): Request
{
    $path = $pathOverride ?? $request->path;

    if (str_starts_with($path, '/public/api')) {
        $path = substr($path, strlen('/public/api')) ?: '/';
    }

    if (str_ends_with($path, '/index.php')) {
        $path = preg_replace('#/index\.php$#', '', $path) ?: '/';
    }

    if (preg_match('#^/publications/?$#', $path)) {
        $path = '/publications';
    }

    return new Request($request->method, $path, $request->headers, $request->body, $request->correlationId, $request->query);
}
