<?php

return [
    'app' => [
        'timezone' => 'UTC',
    ],
    'database' => [
        'driver' => 'mysql',
        'host' => '127.0.0.1',
        'port' => 3306,
        'dbname' => 'nol_module_pubm',
        'username' => 'nol_module_pubm',
        'password' => 'CHANGE_ME',
        'charset' => 'utf8mb4',
    ],
    'api' => [
        'require_api_key' => false,
        'api_key' => 'local-dev-key',
    ],
    'comml' => [
        'enabled' => true,
        'base_url' => 'http://127.0.0.1:18080',
        'route_path' => '/api/route.php',
        'timeout_seconds' => 10,
    ],
];
