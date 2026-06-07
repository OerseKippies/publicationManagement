<?php

declare(strict_types=1);

require dirname(__DIR__, 2) . '/src-php/bootstrap.php';

pubm_bootstrap();

use PubM\Application;
use PubM\Http\Request;
use PubM\Infrastructure\Correlation;

$correlationId = Correlation::resolve($_SERVER['HTTP_X_CORRELATION_ID'] ?? null);
$request = Request::fromGlobals($correlationId);
(new Application(pubm_config_path()))->handle($request)->send();
