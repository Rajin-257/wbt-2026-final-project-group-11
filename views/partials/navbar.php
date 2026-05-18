<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$cartCount = 0;
if (!empty($_SESSION['user_id']) && ($_SESSION['role'] ?? '') === 'customer') {
    require_once __DIR__ . '/../../config/app.php';
    require_once __DIR__ . '/../../model/CartModel.php';
    $cartCount = (new CartModel())->getCartCount((int) $_SESSION['user_id']);
}
?>
<nav class="navbar" aria-label="Main navigation">
    <div class="navbar__inner">
        <a href="index.php" class="navbar__brand">
            <span class="navbar__brand-text">Medicine Shop</span>
        </a>
        <div class="navbar__actions">
            <a class="navbar__btn navbar__btn--ghost" href="index.php">Home</a>

            <?php if (!empty($_SESSION['user_id'])): ?>

                <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
                    <a class="navbar__btn navbar__btn--ghost"
                       href="index.php?page=admin/dashboard">Admin Panel</a>
                <?php endif; ?>

                <?php if (($_SESSION['role'] ?? '') === 'customer'): ?>
                    <a class="navbar__btn navbar__btn--ghost"
                       href="index.php?page=my_orders">My Orders</a>
                    <a class="navbar__btn navbar__btn--ghost navbar__cart"
                       href="index.php?page=cart">
                        Cart<?php if ($cartCount > 0): ?>
                            <span class="cart-badge"><?= $cartCount ?></span>
                        <?php endif; ?>
                    </a>
                <?php endif; ?>

                <a class="navbar__btn navbar__btn--ghost"
                   href="index.php?page=profile">
                    <?= e($_SESSION['name'] ?? '') ?>
                </a>

                <a class="navbar__btn navbar__btn--primary"
                   href="index.php?page=logout">Logout</a>

            <?php else: ?>
                <a class="navbar__btn navbar__btn--ghost"
                   href="index.php?page=login">Login</a>
                <a class="navbar__btn navbar__btn--primary"
                   href="index.php?page=register">Register</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
