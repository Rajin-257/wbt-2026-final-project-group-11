<?php

/**
 * CSRF protection — session token, form fields, and X-CSRF-Token header for AJAX.
 */

function csrf_ensure_session(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function csrf_token(): string
{
    csrf_ensure_session();
    if (empty($_SESSION['_csrf_token'])) {
        $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf_token'];
}

function csrf_regenerate(): void
{
    csrf_ensure_session();
    $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}

function csrf_meta(): string
{
    return '<meta name="csrf-token" content="' . e(csrf_token()) . '">';
}

function csrf_get_submitted_token(): ?string
{
    if (!empty($_POST['_csrf'])) {
        return (string) $_POST['_csrf'];
    }

    $headers = [
        $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '',
        $_SERVER['HTTP_X_CSRF-TOKEN'] ?? '',
    ];

    foreach ($headers as $value) {
        if ($value !== '') {
            return (string) $value;
        }
    }

    return null;
}

function csrf_verify(): bool
{
    csrf_ensure_session();
    $stored    = $_SESSION['_csrf_token'] ?? '';
    $submitted = csrf_get_submitted_token();

    return $submitted !== null
        && $stored !== ''
        && hash_equals($stored, $submitted);
}

function csrf_require(): void
{
    if (csrf_verify()) {
        return;
    }

    http_response_code(403);

    $wantsJson = (
        str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json')
        || str_contains($_SERVER['CONTENT_TYPE'] ?? '', 'application/json')
        || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest')
    );

    if ($wantsJson) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => false,
            'message' => 'Invalid or missing security token. Please refresh the page and try again.',
        ]);
    } else {
        $_SESSION['csrf_error'] = 'Security token invalid or expired. Please refresh the page and try again.';
        $referer = $_SERVER['HTTP_REFERER'] ?? 'index.php';
        header('Location: ' . $referer);
    }
    exit;
}

function csrf_take_flash_error(): string
{
    csrf_ensure_session();
    $msg = $_SESSION['csrf_error'] ?? '';
    unset($_SESSION['csrf_error']);
    return $msg;
}
