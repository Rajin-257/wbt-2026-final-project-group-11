<!DOCTYPE html>
<html>
<head>
    <title>Purchase History — Admin</title>
    <link rel="stylesheet" href="public/css/admin.css">
</head>
<body>

<?php require_once __DIR__ . '/../partials/sidebar.php'; ?>

<div class="main">
    <div class="container">
        <h1>All Customers Purchase History</h1>

        <?php if (empty($orders)): ?>
            <p>No completed orders yet.</p>
        <?php else: ?>
            <?php foreach ($orders as $order): ?>
            <div class="form-box" style="max-width:100%; margin-bottom:20px;">
                <h3>Order #<?= $order['id'] ?> — <?= htmlspecialchars($order['customer_name']) ?></h3>
                <p>Date: <?= $order['order_date'] ?> |
                   Payment: <?= htmlspecialchars($order['payment_method']) ?> |
                   Total: <?= $order['total_amount'] ?> BDT</p>
                <p>Shipping: <?= htmlspecialchars($order['shipping_address']) ?></p>
                <br>
                <table>
                    <thead>
                        <tr>
                            <th>Medicine</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($order_items[$order['id']] as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['medicine_name']) ?></td>
                            <td><?= $item['quantity'] ?></td>
                            <td><?= $item['unit_price'] ?> BDT</td>
                            <td><?= $item['quantity'] * $item['unit_price'] ?> BDT</td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

</body>
</html>