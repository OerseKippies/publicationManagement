<?php

declare(strict_types=1);

require dirname(__DIR__, 2) . '/src-php/bootstrap.php';

pubm_bootstrap();

use PubM\Application;
use PubM\Http\Request;
use PubM\Infrastructure\Correlation;

$correlationId = Correlation::resolve(null);
$request = new Request('GET', '/health', [], null, $correlationId);
(new Application(pubm_config_path()))->handle($request)->send();
