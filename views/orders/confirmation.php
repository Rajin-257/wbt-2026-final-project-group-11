<?php
// views/orders/confirmation.php
$pageTitle = 'Order Confirmed';
require ROOT_PATH . '/views/header.php';
?>

<div class="page-hero page-hero--success">
    <div class="success-icon">✓</div>
    <h1>Order Placed Successfully!</h1>
    <p>Your order <strong>#<?= $order['id'] ?></strong> is pending admin approval.</p>
</div>

<div class="container">
    <div class="steps">
        <div class="step step--done"><span>✓</span> Address</div>
        <div class="step step--done"><span>✓</span> Invoice</div>
        <div class="step step--done"><span>✓</span> Payment</div>
        <div class="step step--active"><span>✓</span> Confirmation</div>
    </div>

    <div class="confirmation-layout">
        <div class="confirmation-card">
            <div class="confirmation-card__header">
                <h2>Order Receipt</h2>
                <span class="status-badge status-badge--<?= $order['status'] ?>"><?= ucfirst($order['status']) ?></span>
            </div>

            <div class="confirmation-meta">
                <div>
                    <small>Order ID</small>
                    <strong>#<?= $order['id'] ?></strong>
                </div>
                <div>
                    <small>Date</small>
                    <strong><?= date('d M Y, h:i A', strtotime($order['order_date'])) ?></strong>
                </div>
                <div>
                    <small>Payment</small>
                    <strong><?= e($order['payment_method']) ?></strong>
                </div>
                <div>
                    <small>Transaction ID</small>
                    <strong><?= e($order['transaction_id'] ?? 'N/A') ?></strong>
                </div>
            </div>

            <h3>Medicines Ordered</h3>
            <table class="invoice-table">
                <thead>
                    <tr>
                        <th>Medicine</th>
                        <th>Vendor</th>
                        <th>Unit Price</th>
                        <th>Qty</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($order['items'] as $item): ?>
                    <tr>
                        <td><?= e($item['name']) ?></td>
                        <td><?= e($item['vendor_name']) ?></td>
                        <td>৳<?= number_format($item['unit_price'], 2) ?></td>
                        <td><?= $item['quantity'] ?></td>
                        <td>৳<?= number_format($item['unit_price'] * $item['quantity'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4"><strong>Total Amount</strong></td>
                        <td><strong>৳<?= number_format($order['total_amount'], 2) ?></strong></td>
                    </tr>
                </tfoot>
            </table>

            <div class="address-preview" style="margin-top:1.5rem;">
                <strong>Shipping Address:</strong>
                <p><?= nl2br(e($order['shipping_address'])) ?></p>
            </div>

            <div class="pending-notice">
                <span class="pending-notice__icon">⏳</span>
                <div>
                    <strong>Awaiting Admin Approval</strong>
                    <p>Your order is currently being reviewed. You'll be able to track its status under "My Orders".</p>
                </div>
            </div>
        </div>
    </div>

    <div class="confirmation-actions">
        <a href="<?= BASE_URL ?>/index.php?page=my_orders" class="btn btn--primary">View My Orders</a>
        <a href="<?= BASE_URL ?>/index.php?page=home"      class="btn btn--outline">Continue Shopping</a>
    </div>
</div>

<?php require ROOT_PATH . '/views/footer.php'; ?>
