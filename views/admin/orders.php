<!DOCTYPE html>
<html>
<head>
    <title>Orders — Admin</title>
    <link rel="stylesheet" href="public/css/admin.css">
</head>
<body>

<?php require_once __DIR__ . '/../partials/sidebar.php'; ?>

<div class="main">
    <div class="container">
        <h1>All Purchase Requests</h1>

        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Customer</th>
                    <th>Total</th>
                    <th>Shipping Address</th>
                    <th>Payment</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($orders)): ?>
                    <tr><td colspan="8">No orders found.</td></tr>
                <?php else: ?>
                    <?php foreach ($orders as $order): ?>
                    <tr id="order-row-<?= $order['id'] ?>">
                        <td><?= $order['id'] ?></td>
                        <td><?= htmlspecialchars($order['customer_name']) ?></td>
                        <td><?= $order['total_amount'] ?> BDT</td>
                        <td><?= htmlspecialchars($order['shipping_address']) ?></td>
                        <td><?= htmlspecialchars($order['payment_method']) ?></td>
                        <td><?= $order['order_date'] ?></td>
                        <td>
                            <span class="badge badge-<?= $order['status'] ?>"
                                  id="badge-<?= $order['id'] ?>">
                                <?= ucfirst($order['status']) ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($order['status'] === 'pending'): ?>
                                <button class="btn btn-success"
                                        onclick="updateStatus(<?= $order['id'] ?>, 'accepted')">
                                    Accept
                                </button>
                                <button class="btn btn-danger"
                                        onclick="updateStatus(<?= $order['id'] ?>, 'rejected')">
                                    Reject
                                </button>
                            <?php else: ?>
                                <span>—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function updateStatus(orderId, status) {
    if (!confirm('Mark this order as ' + status + '?')) return;

    fetch('index.php?page=admin/orders/update-status', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ order_id: orderId, status: status })
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
        if (data.success) {
            var badge = document.getElementById('badge-' + orderId);
            badge.textContent = data.status.charAt(0).toUpperCase() + data.status.slice(1);
            badge.className   = 'badge badge-' + data.status;

            var row = document.getElementById('order-row-' + orderId);
            row.querySelector('td:last-child').innerHTML = '<span>—</span>';
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(function() {
        alert('Something went wrong. Please try again.');
    });
}
</script>

</body>
</html>