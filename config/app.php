<?php

require_once __DIR__ . '/csrf.php';

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__));
}

if (!function_exists('app_base_url')) {
    function app_base_url(): string
    {
        $dir = dirname(str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? ''));
        if (basename($dir) === 'api') {
            $dir = dirname($dir);
        }
        return rtrim($dir, '/');
    }
}

if (!defined('BASE_URL')) {
    define('BASE_URL', app_base_url());
}

if (!function_exists('e')) {
    function e(?string $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('redirect')) {
    function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit();
    }
}
