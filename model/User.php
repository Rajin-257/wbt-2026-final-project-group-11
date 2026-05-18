<?php

require_once __DIR__ . '/../config/db.php';

/* ─── CREATE ─────────────────────────────────────────────────────────────── */

function user_create(array $data): bool|string
{
    $conn = getConnection();

    // unique-email check
    $chk = mysqli_prepare($conn, 'SELECT id FROM users WHERE email = ?');
    mysqli_stmt_bind_param($chk, 's', $data['email']);
    mysqli_stmt_execute($chk);
    mysqli_stmt_store_result($chk);
    if (mysqli_stmt_num_rows($chk) > 0) {
        mysqli_stmt_close($chk);
        mysqli_close($conn);
        return 'email_taken';
    }
    mysqli_stmt_close($chk);

    $name    = trim($data['name']);
    $email   = trim($data['email']);
    $hash    = password_hash($data['password'], PASSWORD_DEFAULT);
    $role    = in_array($data['role'], ['admin', 'customer']) ? $data['role'] : 'customer';
    $address = trim($data['address'] ?? '');
    $phone   = trim($data['phone']   ?? '');

    $stmt = mysqli_prepare(
        $conn,
        'INSERT INTO users (name, email, password_hash, role, address, phone) VALUES (?, ?, ?, ?, ?, ?)'
    );
    mysqli_stmt_bind_param($stmt, 'ssssss', $name, $email, $hash, $role, $address, $phone);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $ok ? true : 'db_error';
}

/* ─── FIND ───────────────────────────────────────────────────────────────── */

function user_find_by_email(string $email): array|false
{
    $conn = getConnection();
    $stmt = mysqli_prepare($conn, 'SELECT * FROM users WHERE email = ? LIMIT 1');
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user   = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $user ?: false;
}

function user_find_by_id(int $id): array|false
{
    $conn = getConnection();
    $stmt = mysqli_prepare($conn, 'SELECT * FROM users WHERE id = ? LIMIT 1');
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user   = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $user ?: false;
}

/* ─── LOGIN ──────────────────────────────────────────────────────────────── */

function user_login(string $email, string $password): array|false
{
    $user = user_find_by_email($email);
    if (!$user) return false;
    if (!password_verify($password, $user['password_hash'])) return false;

    unset($user['password_hash']);
    return $user;
}

/* ─── REMEMBER-ME TOKEN ──────────────────────────────────────────────────── */

function user_save_token(int $userId, string $token): void
{
    $conn = getConnection();
    $hash = hash('sha256', $token);
    $stmt = mysqli_prepare($conn, 'UPDATE users SET remember_token = ? WHERE id = ?');

    // remember_token column may not exist yet; graceful ignore
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'si', $hash, $userId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
    mysqli_close($conn);
}

function user_find_by_token(string $token): array|false
{
    $conn = getConnection();
    $hash = hash('sha256', $token);
    $stmt = mysqli_prepare($conn, 'SELECT * FROM users WHERE remember_token = ? LIMIT 1');
    if (!$stmt) { mysqli_close($conn); return false; }
    mysqli_stmt_bind_param($stmt, 's', $hash);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user   = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $user ?: false;
}

function user_clear_token(int $userId): void
{
    $conn = getConnection();
    $null = null;
    $stmt = mysqli_prepare($conn, 'UPDATE users SET remember_token = NULL WHERE id = ?');
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'i', $userId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
    mysqli_close($conn);
}

/* ─── UPDATE PROFILE ─────────────────────────────────────────────────────── */

function user_update_profile(int $id, array $data): bool
{
    $conn    = getConnection();
    $name    = trim($data['name']);
    $email   = trim($data['email']);
    $address = trim($data['address'] ?? '');
    $phone   = trim($data['phone']   ?? '');

    // unique-email check excluding self
    $chk = mysqli_prepare($conn, 'SELECT id FROM users WHERE email = ? AND id != ?');
    mysqli_stmt_bind_param($chk, 'si', $email, $id);
    mysqli_stmt_execute($chk);
    mysqli_stmt_store_result($chk);
    if (mysqli_stmt_num_rows($chk) > 0) {
        mysqli_stmt_close($chk);
        mysqli_close($conn);
        return false;
    }
    mysqli_stmt_close($chk);

    $stmt = mysqli_prepare(
        $conn,
        'UPDATE users SET name = ?, email = ?, address = ?, phone = ? WHERE id = ?'
    );
    mysqli_stmt_bind_param($stmt, 'ssssi', $name, $email, $address, $phone, $id);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $ok;
}

function user_update_picture(int $id, string $picturePath): bool
{
    $conn = getConnection();
    $stmt = mysqli_prepare($conn, 'UPDATE users SET profile_picture = ? WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'si', $picturePath, $id);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $ok;
}

function user_change_password(int $id, string $currentPassword, string $newPassword): string
{
    $user = user_find_by_id($id);
    if (!$user) return 'not_found';
    if (!password_verify($currentPassword, $user['password_hash'])) return 'wrong_password';

    $conn = getConnection();
    $hash = password_hash($newPassword, PASSWORD_DEFAULT);
    $stmt = mysqli_prepare($conn, 'UPDATE users SET password_hash = ? WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'si', $hash, $id);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $ok ? 'ok' : 'db_error';
}

/* ─── ALL / DELETE (admin) ───────────────────────────────────────────────── */

function user_all(): array
{
    $conn   = getConnection();
    $result = mysqli_query($conn, 'SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC');
    $rows   = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_close($conn);
    return $rows;
}

function user_delete(int $id): bool
{
    $conn = getConnection();
    $stmt = mysqli_prepare($conn, 'DELETE FROM users WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'i', $id);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $ok;
}
