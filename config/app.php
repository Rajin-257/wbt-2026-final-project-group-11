<?php
// config/app.php

define('BASE_URL', 'http://localhost/task3_23-54596-3/task3');
if (!defined('ROOT_PATH')) define('ROOT_PATH', dirname(__DIR__));
define('UPLOAD_PATH', ROOT_PATH . '/public/uploads/medicines/');
define('MAX_IMAGE_SIZE', 2 * 1024 * 1024); 


function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string {
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token()) . '">';
}

function verify_csrf(): void {
    $token = $_POST['csrf_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
    if (!hash_equals(csrf_token(), $token)) {
        http_response_code(403);
        die(json_encode(['error' => 'Invalid CSRF token.']));
    }
}


function e(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}


function redirect(string $url): void {
    header('Location: ' . $url);
    exit;
}


function require_login(): void {
    if (empty($_SESSION['user_id'])) {
        redirect(BASE_URL . '/index.php?page=login&redirect=' . urlencode($_SERVER['REQUEST_URI']));
    }
}

function require_customer(): void {
    require_login();
    if ($_SESSION['role'] !== 'customer') {
        redirect(BASE_URL . '/index.php?page=home');
    }
}
