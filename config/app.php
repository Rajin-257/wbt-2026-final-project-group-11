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

/** Escape text for HTML body, attributes, and quoted contexts. */
if (!function_exists('e')) {
    function e(?string $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

/** Escape a relative URL for use in href/src (blocks javascript: and data: URIs). */
if (!function_exists('e_url')) {
    function e_url(?string $path): string
    {
        $path = trim((string) $path);
        if ($path === '') {
            return '';
        }
        if (preg_match('#^\s*(javascript|data|vbscript|file):#i', $path)) {
            return '';
        }
        if (str_contains($path, '..')) {
            return '';
        }
        return htmlspecialchars($path, ENT_QUOTES, 'UTF-8');
    }
}

/** Safe CSS class fragment; optional whitelist for enum values (role, status, type). */
if (!function_exists('e_css')) {
    function e_css(?string $value, array $allowed = []): string
    {
        $value = (string) $value;
        if ($allowed !== []) {
            return in_array($value, $allowed, true) ? $value : '';
        }
        return preg_replace('/[^a-z0-9_-]/i', '', $value) ?? '';
    }
}

if (!function_exists('security_headers')) {
    function security_headers(): void
    {
        if (headers_sent()) {
            return;
        }
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: SAMEORIGIN');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header(
            "Content-Security-Policy: default-src 'self'; "
            . "script-src 'self' 'unsafe-inline'; "
            . "style-src 'self' 'unsafe-inline'; "
            . "img-src 'self' data:; "
            . "base-uri 'self'; "
            . "form-action 'self'; "
            . "frame-ancestors 'self'"
        );
    }
}

security_headers();

if (!function_exists('redirect')) {
    function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit();
    }
}
