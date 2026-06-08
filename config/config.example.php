<?php

return [
    'app' => [
        'timezone' => 'UTC',
    ],
    'database' => [
        'driver' => 'mysql',
        'host' => '127.0.0.1',
        'port' => 3306,
        'dbname' => 'nl_module_pubM',
        'username' => 'nol_module_pubm',
        'password' => 'CHANGE_ME',
        'charset' => 'utf8mb4',
    ],
    'api' => [
        'require_api_key' => false,
        'api_key' => 'local-dev-key',
    ],
    'comml' => [
        'enabled' => false,
        'base_url' => '',
        'timeout_seconds' => 5,
    ],
];
