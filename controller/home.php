<?php

require_once __DIR__ . '/../model/User.php';

/* ─── helpers ─────────────────────────────────────────────────────────────── */

function require_login(): void
{
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['user_id'])) {
        header('Location: index.php?page=login');
        exit();
    }
}

function require_customer(): void
{
    require_login();
    if (($_SESSION['role'] ?? '') !== 'customer') {
        header('Location: index.php?page=login');
        exit();
    }
}

/* ─── auto-login via remember-me cookie ──────────────────────────────────── */

function try_remember_me(): void
{
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (!empty($_SESSION['user_id'])) return;

    $token = $_COOKIE['remember_token'] ?? '';
    if ($token === '') return;

    $user = user_find_by_token($token);
    if (!$user) return;

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['name']    = $user['name'];
    $_SESSION['role']    = $user['role'];
    $_SESSION['email']   = $user['email'];
}

/* ─── home ───────────────────────────────────────────────────────────────── */

function home(): void
{
    if (session_status() === PHP_SESSION_NONE) session_start();
    try_remember_me();
    $title = 'Home — Medicine Shop';
    $view  = __DIR__ . '/../views/home.php';
    $layout = __DIR__ . '/../views/layout.php';
    include $layout;
}

/* ─── register ───────────────────────────────────────────────────────────── */

function register(): void
{
    if (session_status() === PHP_SESSION_NONE) session_start();

    $errors  = [];
    $success = '';
    $old     = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        csrf_require();
        $name     = trim($_POST['name']     ?? '');
        $email    = trim($_POST['email']    ?? '');
        $password = $_POST['password']      ?? '';
        $address  = trim($_POST['address']  ?? '');
        $phone    = trim($_POST['phone']    ?? '');
        $role     = $_POST['role']          ?? '';

        $old = compact('name', 'email', 'address', 'phone', 'role');

        // ── server-side validation ──
        if ($name === '')                         $errors[] = 'Name is required.';
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL))
                                                  $errors[] = 'A valid email is required.';
        if (strlen($password) < 8)                $errors[] = 'Password must be at least 8 characters.';
        if (!in_array($role, ['admin', 'customer'])) $errors[] = 'Please select a valid role.';

        if (empty($errors)) {
            $result = user_create([
                'name'     => $name,
                'email'    => $email,
                'password' => $password,
                'address'  => $address,
                'phone'    => $phone,
                'role'     => $role,
            ]);

            if ($result === true) {
                header('Location: index.php?page=login&registered=1');
                exit();
            } elseif ($result === 'email_taken') {
                $errors[] = 'That email address is already registered.';
            } else {
                $errors[] = 'Something went wrong. Please try again.';
            }
        }
    }

    $title  = 'Register — Medicine Shop';
    $view   = __DIR__ . '/../views/auth/registration.php';
    $layout = __DIR__ . '/../views/layout.php';
    include $layout;
}

/* ─── login ──────────────────────────────────────────────────────────────── */

function login(): void
{
    if (session_status() === PHP_SESSION_NONE) session_start();

    // already logged in?
    if (!empty($_SESSION['user_id'])) {
        _redirect_after_login($_SESSION['role']);
    }

    $errors  = [];
    $success = '';

    if (isset($_GET['registered'])) {
        $success = 'Account created! Please sign in.';
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        csrf_require();
        $email    = trim($_POST['email']    ?? '');
        $password = $_POST['password']      ?? '';
        $remember = !empty($_POST['remember']);

        // ── server-side validation ──
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL))
            $errors[] = 'Please enter a valid email address.';
        if ($password === '')
            $errors[] = 'Password is required.';

        if (empty($errors)) {
            $user = user_login($email, $password);

            if ($user === false) {
                $errors[] = 'Invalid email or password.';
            } else {
                // populate session
                session_regenerate_id(true);
                csrf_regenerate();
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name']    = $user['name'];
                $_SESSION['role']    = $user['role'];
                $_SESSION['email']   = $user['email'];

                // remember-me cookie (30 days)
                if ($remember) {
                    $token = bin2hex(random_bytes(32));
                    user_save_token((int)$user['id'], $token);
                    setcookie(
                        'remember_token',
                        $token,
                        time() + 30 * 24 * 3600,
                        '/',
                        '',
                        false,  // secure — set true on HTTPS
                        true    // httpOnly
                    );
                }

                _redirect_after_login($user['role']);
            }
        }
    }

    $title  = 'Sign in — Medicine Shop';
    $view   = __DIR__ . '/../views/auth/login.php';
    $layout = __DIR__ . '/../views/layout.php';
    include $layout;
}

function _redirect_after_login(string $role): void
{
    if ($role === 'admin') {
        header('Location: index.php?page=admin/dashboard');
    } else {
        header('Location: index.php');
    }
    exit();
}

/* ─── logout ─────────────────────────────────────────────────────────────── */

function logout(): void
{
    if (session_status() === PHP_SESSION_NONE) session_start();

    // clear remember-me
    if (!empty($_SESSION['user_id'])) {
        user_clear_token((int)$_SESSION['user_id']);
    }

    setcookie('remember_token', '', time() - 3600, '/');
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    csrf_regenerate();

    header('Location: index.php?page=login');
    exit();
}

/* ─── profile ────────────────────────────────────────────────────────────── */

function profile(): void
{
    require_login();

    $errors  = [];
    $success = '';
    $userId  = (int)$_SESSION['user_id'];
    $user    = user_find_by_id($userId);

    if (!$user) {
        logout();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        csrf_require();
        $action = $_POST['action'] ?? 'update_profile';

        /* ── update basic info ── */
        if ($action === 'update_profile') {
            $name    = trim($_POST['name']    ?? '');
            $email   = trim($_POST['email']   ?? '');
            $address = trim($_POST['address'] ?? '');
            $phone   = trim($_POST['phone']   ?? '');

            if ($name === '')
                $errors[] = 'Name is required.';
            if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL))
                $errors[] = 'A valid email is required.';

            // handle profile picture upload
            $picturePath = $user['profile_picture'];
            if (!empty($_FILES['profile_picture']['name'])) {
                $file    = $_FILES['profile_picture'];
                $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                if (!in_array($file['type'], $allowed)) {
                    $errors[] = 'Profile picture must be a JPEG, PNG, GIF, or WEBP image.';
                } elseif ($file['size'] > 2 * 1024 * 1024) {
                    $errors[] = 'Profile picture must be smaller than 2 MB.';
                } else {
                    $uploadDir = __DIR__ . '/../public/uploads/';
                    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

                    $ext         = pathinfo($file['name'], PATHINFO_EXTENSION);
                    $filename    = 'user_' . $userId . '_' . time() . '.' . $ext;
                    $destination = $uploadDir . $filename;

                    if (move_uploaded_file($file['tmp_name'], $destination)) {
                        $picturePath = 'public/uploads/' . $filename;
                    } else {
                        $errors[] = 'Failed to upload picture. Please try again.';
                    }
                }
            }

            if (empty($errors)) {
                $updated = user_update_profile($userId, [
                    'name'    => $name,
                    'email'   => $email,
                    'address' => $address,
                    'phone'   => $phone,
                ]);

                if ($updated === false) {
                    $errors[] = 'That email address is already used by another account.';
                } else {
                    // save picture separately if changed
                    if ($picturePath !== $user['profile_picture']) {
                        user_update_picture($userId, $picturePath);
                        $user['profile_picture'] = $picturePath;
                    }
                    // refresh session
                    $_SESSION['name']  = $name;
                    $_SESSION['email'] = $email;
                    $user['name']      = $name;
                    $user['email']     = $email;
                    $user['address']   = $address;
                    $user['phone']     = $phone;
                    $success = 'Profile updated successfully.';
                }
            }
        }

        /* ── change password ── */
        if ($action === 'change_password') {
            $current  = $_POST['current_password']  ?? '';
            $newPass  = $_POST['new_password']       ?? '';
            $confirm  = $_POST['confirm_password']   ?? '';

            if ($current === '')
                $errors[] = 'Current password is required.';
            if (strlen($newPass) < 8)
                $errors[] = 'New password must be at least 8 characters.';
            if ($newPass !== $confirm)
                $errors[] = 'New passwords do not match.';

            if (empty($errors)) {
                $result = user_change_password($userId, $current, $newPass);
                if ($result === 'wrong_password') {
                    $errors[] = 'Current password is incorrect.';
                } elseif ($result !== 'ok') {
                    $errors[] = 'Something went wrong. Please try again.';
                } else {
                    $success = 'Password changed successfully.';
                }
            }
        }

        // re-fetch user after updates
        $user = user_find_by_id($userId);
    }

    $title  = 'My Profile — Medicine Shop';
    $view   = __DIR__ . '/../views/auth/profile.php';
    $layout = __DIR__ . '/../views/layout.php';
    include $layout;
}
