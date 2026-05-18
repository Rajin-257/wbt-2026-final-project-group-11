<?php
// views/checkout/payment.php
$pageTitle = 'Select Payment';
require ROOT_PATH . '/views/ashiqur/header.php';
?>

<div class="page-hero">
    <h1>Payment Method</h1>
    <p>Choose how you'd like to pay for your order.</p>
</div>

<div class="container">
    <div class="steps">
        <div class="step step--done"><span>✓</span> Address</div>
        <div class="step step--done"><span>✓</span> Invoice</div>
        <div class="step step--active"><span>3</span> Payment</div>
        <div class="step"><span>4</span> Confirmation</div>
    </div>

    <div class="payment-layout">
        <div class="invoice-card">
            <h2>Order Summary</h2>
            <table class="invoice-table">
                <thead>
                    <tr><th>Medicine</th><th>Qty</th><th>Subtotal</th></tr>
                </thead>
                <tbody>
                <?php foreach ($cartItems as $item): ?>
                    <tr>
                        <td><?= e($item['name']) ?></td>
                        <td><?= $item['quantity'] ?></td>
                        <td>৳<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2"><strong>Total</strong></td>
                        <td><strong>৳<?= number_format($cartTotal, 2) ?></strong></td>
                    </tr>
                </tfoot>
            </table>
            <div class="address-preview">
                <strong>Shipping to:</strong>
                <p><?= nl2br(e($_SESSION['pending_address'])) ?></p>
            </div>
        </div>

        <div class="payment-form-card">
            <h2>Select Payment</h2>
            <form id="paymentForm" action="<?= BASE_URL ?>/index.php?page=place_order" method="POST" novalidate>
                <?= csrf_field() ?>

                <div class="payment-methods" id="paymentMethods" role="radiogroup" aria-label="Payment methods">

                    <?php
                    $methods = [
                        'Credit Card'     => ['icon' => '💳', 'desc' => 'Visa, Mastercard, Amex'],
                        'bKash'           => ['icon' => '📱', 'desc' => 'bKash mobile banking'],
                        'Nagad'           => ['icon' => '📲', 'desc' => 'Nagad digital wallet'],
                        'Bank Transfer'   => ['icon' => '🏦', 'desc' => 'Direct bank transfer'],
                        'Cash on Delivery'=> ['icon' => '💵', 'desc' => 'Pay when delivered'],
                    ];
                    foreach ($methods as $value => $meta):
                    ?>
                    <label class="payment-option" for="pm_<?= str_replace(' ', '_', $value) ?>">
                        <input type="radio" name="payment_method"
                               id="pm_<?= str_replace(' ', '_', $value) ?>"
                               value="<?= e($value) ?>">
                        <span class="payment-option__icon"><?= $meta['icon'] ?></span>
                        <span class="payment-option__info">
                            <strong><?= e($value) ?></strong>
                            <small><?= e($meta['desc']) ?></small>
                        </span>
                        <span class="payment-option__check">✓</span>
                    </label>
                    <?php endforeach; ?>

                </div>
                <span class="field-error" id="pmError"></span>

                <div class="checkout-actions" style="margin-top:1.5rem;">
                    <a href="<?= BASE_URL ?>/index.php?page=checkout" class="btn btn--outline">← Back</a>
                    <button type="submit" class="btn btn--primary btn--lg" id="confirmBtn">
                        Confirm Purchase ৳<?= number_format($cartTotal, 2) ?> →
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>

document.querySelectorAll('.payment-option input[type=radio]').forEach(radio => {
    radio.addEventListener('change', function() {
        document.querySelectorAll('.payment-option').forEach(l => l.classList.remove('payment-option--selected'));
        this.closest('.payment-option').classList.add('payment-option--selected');
        document.getElementById('pmError').textContent = '';
    });
});

document.getElementById('paymentForm').addEventListener('submit', function(e) {
    const selected = document.querySelector('input[name="payment_method"]:checked');
    if (!selected) {
        e.preventDefault();
        document.getElementById('pmError').textContent = 'Please select a payment method.';
        document.getElementById('paymentMethods').scrollIntoView({ behavior: 'smooth' });
    }
});
</script>

<?php require ROOT_PATH . '/views/ashiqur/footer.php'; ?>
