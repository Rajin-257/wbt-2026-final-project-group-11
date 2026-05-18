<?php
// views/checkout/index.php
$pageTitle = 'Checkout';
require ROOT_PATH . '/views/ashiqur/header.php';
?>

<div class="page-hero">
    <h1>Checkout</h1>
    <p>Review your order and confirm shipping details.</p>
</div>

<div class="container">
    <div class="steps">
        <div class="step step--active"><span>1</span> Address</div>
        <div class="step"><span>2</span> Invoice</div>
        <div class="step"><span>3</span> Payment</div>
        <div class="step"><span>4</span> Confirmation</div>
    </div>

    <div class="checkout-layout">
        <!-- Invoice Preview -->
        <div class="invoice-card">
            <h2>Order Invoice</h2>
            <table class="invoice-table">
                <thead>
                    <tr>
                        <th>Medicine</th>
                        <th>Unit Price</th>
                        <th>Qty</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($cartItems as $item): ?>
                    <tr>
                        <td><?= e($item['name']) ?> <small class="text-muted"><?= e($item['vendor_name']) ?></small></td>
                        <td>৳<?= number_format($item['price'], 2) ?></td>
                        <td><?= $item['quantity'] ?></td>
                        <td>৳<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3"><strong>Grand Total</strong></td>
                        <td><strong>৳<?= number_format($cartTotal, 2) ?></strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="address-form-card">
            <h2>Shipping Address</h2>
            <form id="checkoutForm" action="<?= BASE_URL ?>/index.php?page=checkout_payment" method="POST" novalidate>
                <?= csrf_field() ?>
                <div class="form-group">
                    <label for="shipping_address">Delivery Address *</label>
                    <textarea id="shipping_address"
                              name="shipping_address"
                              rows="4"
                              class="form-control"
                              placeholder="Enter your full delivery address…"
                              required><?= e($_SESSION['pending_address'] ?? ($userInfo['address'] ?? '')) ?></textarea>
                    <span class="field-error" id="addrError"></span>
                </div>

                <div class="form-group">
                    <label>Contact Phone</label>
                    <input type="text" class="form-control" value="<?= e($userInfo['phone'] ?? '') ?>" readonly disabled>
                    <small>Update your phone in Profile if needed.</small>
                </div>

                <div class="checkout-actions">
                    <a href="<?= BASE_URL ?>/index.php?page=cart" class="btn btn--outline">← Back to Cart</a>
                    <button type="submit" class="btn btn--primary">Confirm Address & View Invoice →</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('checkoutForm').addEventListener('submit', function(e) {
    const addr = document.getElementById('shipping_address').value.trim();
    const err  = document.getElementById('addrError');
    if (!addr) {
        e.preventDefault();
        err.textContent = 'Shipping address cannot be empty.';
        document.getElementById('shipping_address').focus();
        return;
    }
    err.textContent = '';
});
</script>

<?php require ROOT_PATH . '/views/ashiqur/footer.php'; ?>
