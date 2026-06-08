<?php

declare(strict_types=1);

$root = dirname(__DIR__);
require $root . '/src-php/Autoloader.php';
PubM\Autoloader::register($root . '/src-php');

use PubM\Application;
use PubM\Http\Request;

$app = new Application($root . '/config/config.php');
$health = $app->handle(new Request('GET', '/health', [], null, 'smoke'));
$copilot = $app->handle(new Request('GET', '/api/copilot/dashboard', [], null, 'smoke'));
echo "health: {$health->statusCode}\n" . json_encode($health->body, JSON_PRETTY_PRINT) . "\n\ncopilot: {$copilot->statusCode}\n" . json_encode($copilot->body, JSON_PRETTY_PRINT) . "\n";
