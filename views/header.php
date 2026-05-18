<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'MediShop') ?> — MediShop</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/style.css">
</head>
<body>

<nav class="navbar">
    <a class="navbar__brand" href="<?= BASE_URL ?>/index.php">
        <span class="navbar__logo">✚</span> MediShop
    </a>
    <div class="navbar__links">
        <a href="<?= BASE_URL ?>/index.php?page=home">Home</a>
        <?php if (!empty($_SESSION['user_id'])): ?>
            <a href="<?= BASE_URL ?>/index.php?page=my_orders">My Orders</a>
            <a href="<?= BASE_URL ?>/index.php?page=cart" class="navbar__cart">
                🛒 Cart
                <?php
                    $cartCountDisplay = 0;
                    if (!empty($_SESSION['user_id']) && ($_SESSION['role'] ?? '') === 'customer') {
                        require_once ROOT_PATH . '/models/CartModel.php';
                        $cartCountDisplay = (new CartModel())->getCartCount((int)$_SESSION['user_id']);
                    }
                ?>
                <?php if ($cartCountDisplay > 0): ?>
                    <span class="cart-badge"><?= $cartCountDisplay ?></span>
                <?php endif; ?>
            </a>
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
