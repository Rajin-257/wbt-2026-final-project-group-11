<?php
// views/orders/my_orders.php
$pageTitle = 'My Orders';
require ROOT_PATH . '/views/ashiqur/header.php';
?>

<div class="page-hero">
    <h1>My Orders</h1>
    <p>Track all your medicine orders and their statuses.</p>
</div>

<div class="container">
<?php if (empty($orders)): ?>
    <div class="empty-state">
        <div class="empty-state__icon">📦</div>
        <h2>No orders yet</h2>
        <p>Once you place an order it will appear here.</p>
        <a href="<?= BASE_URL ?>/index.php" class="btn btn--primary">Browse Medicines</a>
    </div>
<?php else: ?>
    <div class="orders-list">
    <?php foreach ($orders as $o): ?>
        <div class="order-card">
            <div class="order-card__header">
                <div>
                    <span class="order-id">Order #<?= $o['id'] ?></span>
                    <span class="order-date"><?= date('d M Y', strtotime($o['order_date'])) ?></span>
                </div>
                <span class="status-badge status-badge--<?= $o['status'] ?>"><?= ucfirst($o['status']) ?></span>
            </div>
            <div class="order-card__body">
                <div>
                    <small>Payment</small>
                    <strong><?= e($o['payment_method']) ?></strong>
                </div>
                <div>
                    <small>Transaction ID</small>
                    <strong><?= e($o['transaction_id'] ?? 'N/A') ?></strong>
                </div>
                <div>
                    <small>Total</small>
                    <strong class="order-total">৳<?= number_format($o['total_amount'], 2) ?></strong>
                </div>
            </div>
            <div class="order-card__footer">
                <small><strong>Shipped to:</strong> <?= e(mb_strimwidth($o['shipping_address'], 0, 80, '…')) ?></small>
            </div>
        </div>
    <?php endforeach; ?>
    </div>
<?php endif; ?>
</div>

<?php require ROOT_PATH . '/views/ashiqur/footer.php'; ?>
