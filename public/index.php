<?php

use App\Kernel;
use inc\User;

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS"); // Add other methods as needed
header("Access-Control-Allow-Headers: Content-Type, Authorization, Cache-Control, Connection, Content-Encoding"); // Include necessary headers

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}

$pattern = "/\/[^\/]+\.[^\/]+$/";
if (!preg_match($pattern, $_SERVER['REQUEST_URI'])) {
    // Include engine.
    match (true) {
        file_exists($_SERVER['DOCUMENT_ROOT'] . '/engine/mini.php') => require_once $_SERVER['DOCUMENT_ROOT'] . '/engine/mini.php', // Production environment
        file_exists(__DIR__ . '/engine/ini.php')                   => require_once __DIR__ . '/engine/ini.php', // Local environment
        default                                                    => false,
    };
}

if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/engine/mini.php')) {
    $main_user = User::getAuthorizedUser();
    $language = $site->getCurrentLang();
}

require_once dirname(__DIR__) . '/vendor/autoload_runtime.php';

return fn(array $context) => new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
