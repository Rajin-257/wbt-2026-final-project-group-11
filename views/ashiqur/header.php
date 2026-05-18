<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!defined('ROOT_PATH')) {
    require_once dirname(__DIR__, 2) . '/config/app.php';
}
if (function_exists('try_remember_me')) {
    try_remember_me();
}

$cartCountDisplay = 0;
if (!empty($_SESSION['user_id']) && ($_SESSION['role'] ?? '') === 'customer') {
    require_once ROOT_PATH . '/model/CartModel.php';
    $cartCountDisplay = (new CartModel())->getCartCount((int) $_SESSION['user_id']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Medicine Shop') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/style.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/ashiqur.css">
    <?php include ROOT_PATH . '/views/partials/csrf_head.php'; ?>
    <script src="<?= BASE_URL ?>/public/xss.js"></script>
</head>
<body>

<nav class="navbar">
    <a class="navbar__brand" href="<?= BASE_URL ?>/index.php">
        <span class="navbar__logo">✚</span> Medicine Shop
    </a>
    <div class="navbar__links">
        <a href="<?= BASE_URL ?>/index.php">Home</a>
        <?php if (!empty($_SESSION['user_id'])): ?>
            <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
                <a href="<?= BASE_URL ?>/index.php?page=admin/dashboard">Admin</a>
            <?php endif; ?>
            <?php if (($_SESSION['role'] ?? '') === 'customer'): ?>
                <a href="<?= BASE_URL ?>/index.php?page=my_orders">My Orders</a>
                <a href="<?= BASE_URL ?>/index.php?page=cart" class="navbar__cart">
                    Cart
                    <span class="cart-badge" <?= $cartCountDisplay > 0 ? '' : 'style="display:none"' ?>><?= $cartCountDisplay ?></span>
                </a>
            <?php endif; ?>
            <a href="<?= BASE_URL ?>/index.php?page=profile"><?= e($_SESSION['name'] ?? 'Profile') ?></a>
            <a href="<?= BASE_URL ?>/index.php?page=logout" class="btn btn--outline">Logout</a>
        <?php else: ?>
            <a href="<?= BASE_URL ?>/index.php?page=login" class="btn btn--outline">Login</a>
            <a href="<?= BASE_URL ?>/index.php?page=register" class="btn btn--primary">Register</a>
        <?php endif; ?>
    </div>
</nav>

<main class="main-content">
<?php
if (!empty($_SESSION['checkout_errors'])):
    $errs = $_SESSION['checkout_errors'];
    unset($_SESSION['checkout_errors']);
?>
    <div class="alert alert--error">
        <ul>
            <?php foreach ($errs as $err): ?>
                <li><?= e($err) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
