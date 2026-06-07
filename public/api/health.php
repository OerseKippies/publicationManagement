<?php

declare(strict_types=1);

header('Content-Type: application/json');

$correlationId = $_SERVER['HTTP_X_CORRELATION_ID'] ?? bin2hex(random_bytes(16));

echo json_encode([
    'status' => 'healthy',
    'module' => 'publicationManagement',
    'moduleCode' => 'pubM',
    'version' => 'v1',
    'database' => 'not_checked',
    'correlationId' => $correlationId,
], JSON_THROW_ON_ERROR);
