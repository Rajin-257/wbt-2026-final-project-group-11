<?php

require_once __DIR__ . '/../config/db.php';

function user_create(array $data)
{
    $conn = getConnection();
    $fullName = $data['fullName'];
    $email = $data['email'];
    $hash = password_hash($data['password'], PASSWORD_DEFAULT);
    $role = $data['role'] ?? 'student';

    $sql = 'INSERT INTO users (fullName, email, password, role) VALUES (?, ?, ?, ?)';
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        mysqli_close($conn);
        return false;
    }

    mysqli_stmt_bind_param($stmt, 'ssss', $fullName, $email, $hash, $role);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $ok;
}


function user_find_by_id(int $id)
{
    $conn = getConnection();
    $sql = 'SELECT id, fullName, email, role, createdAt, updatedAt FROM users WHERE id = ?';
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        mysqli_close($conn);
        return null;
    }

    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = $result ? mysqli_fetch_assoc($result) : null;
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $row ?: null;
}

function user_find_by_email(string $email)
{
    $conn = getConnection();
    $sql = 'SELECT * FROM users WHERE email = ?';
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        mysqli_close($conn);
        return null;
    }

    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = $result ? mysqli_fetch_assoc($result) : null;
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $row ?: null;
}


function user_all()
{
    $conn = getConnection();
    $sql = 'SELECT id, fullName, email, role, createdAt, updatedAt FROM users ORDER BY id ASC';
    $result = mysqli_query($conn, $sql);
    $rows = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        mysqli_free_result($result);
    }
    mysqli_close($conn);
    return $rows;
}


function user_update(int $id, array $data)
{
    $conn = getConnection();
    $fullName = $data['fullName'];
    $email = $data['email'];
    $role = $data['role'] ?? 'student';

    if (!empty($data['password'])) {
        $hash = password_hash($data['password'], PASSWORD_DEFAULT);
        $sql = 'UPDATE users SET fullName = ?, email = ?, password = ?, role = ? WHERE id = ?';
        $stmt = mysqli_prepare($conn, $sql);
        $ok = $stmt && mysqli_stmt_bind_param($stmt, 'ssssi', $fullName, $email, $hash, $role, $id)
            && mysqli_stmt_execute($stmt);
    } else {
        $sql = 'UPDATE users SET fullName = ?, email = ?, role = ? WHERE id = ?';
        $stmt = mysqli_prepare($conn, $sql);
        $ok = $stmt && mysqli_stmt_bind_param($stmt, 'sssi', $fullName, $email, $role, $id)
            && mysqli_stmt_execute($stmt);
    }

    if (!empty($stmt)) {
        mysqli_stmt_close($stmt);
    }
    mysqli_close($conn);
    return $ok;
}

function user_delete(int $id)
{
    $conn = getConnection();
    $sql = 'DELETE FROM users WHERE id = ?';
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        mysqli_close($conn);
        return false;
    }

    mysqli_stmt_bind_param($stmt, 'i', $id);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $ok;
}


function user_login(string $email, string $password)
{
    $user = user_find_by_email($email);
    if (!$user || !password_verify($password, $user['password'])) {
        return false;
    }

    unset($user['password']);
    return $user;
}
