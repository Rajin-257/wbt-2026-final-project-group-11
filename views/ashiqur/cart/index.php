<?php
// views/cart/index.php
$pageTitle = 'My Cart';
require ROOT_PATH . '/views/ashiqur/header.php';
?>

<div class="page-hero">
    <h1>Shopping Cart</h1>
    <p>Review your selected medicines before checkout.</p>
</div>

<div class="container">

<?php if (empty($cartItems)): ?>
    <div class="empty-state">
        <div class="empty-state__icon">🛒</div>
        <h2>Your cart is empty</h2>
        <p>Browse our medicine catalogue and add items to your cart.</p>
        <a href="<?= BASE_URL ?>/index.php" class="btn btn--primary">Browse Medicines</a>
    </div>
<?php else: ?>

    <div class="cart-layout">
        <div class="cart-items">
            <table class="cart-table" id="cartTable">
                <thead>
                    <tr>
                        <th>Medicine</th>
                        <th>Vendor</th>
                        <th>Unit Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                        <th>Remove</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($cartItems as $item): ?>
                    <tr class="cart-row" data-cart-id="<?= $item['cart_id'] ?>" data-stock="<?= $item['stock'] ?>">
                        <td class="cart-row__name">
                            <?php if (!empty($item['image_path'])): ?>
                                <img src="<?= BASE_URL . '/' . e_url($item['image_path']) ?>" alt="" class="cart-thumb">
                            <?php else: ?>
                                <div class="cart-thumb cart-thumb--placeholder">💊</div>
                            <?php endif; ?>
                            <?= e($item['name']) ?>
                        </td>
                        <td><?= e($item['vendor_name']) ?></td>
                        <td class="cart-row__price">৳<?= number_format($item['price'], 2) ?></td>
                        <td>
                            <div class="qty-ctrl">
                                <button type="button" class="qty-btn qty-btn--dec" aria-label="Decrease">−</button>
                                <input  type="number"
                                        class="qty-input"
                                        value="<?= $item['quantity'] ?>"
                                        min="1"
                                        max="<?= $item['stock'] ?>"
                                        data-price="<?= $item['price'] ?>">
                                <button type="button" class="qty-btn qty-btn--inc" aria-label="Increase">+</button>
                            </div>
                            <span class="qty-error" style="display:none; color:var(--danger); font-size:0.75rem;"></span>
                        </td>
                        <td class="cart-row__subtotal">৳<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                        <td>
                            <button type="button" class="btn btn--danger btn--sm remove-btn" aria-label="Remove item">✕</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <aside class="cart-summary">
            <h2>Order Summary</h2>
            <div class="summary-row">
                <span>Items</span>
                <span id="summaryCount"><?= array_sum(array_column($cartItems, 'quantity')) ?></span>
            </div>
            <div class="summary-row summary-row--total">
                <span>Total</span>
                <span id="cartTotal">৳<?= number_format($cartTotal, 2) ?></span>
            </div>
            <a href="<?= BASE_URL ?>/index.php?page=checkout" class="btn btn--primary btn--full" id="checkoutBtn">
                Proceed to Checkout →
            </a>
            <a href="<?= BASE_URL ?>/index.php" class="btn btn--outline btn--full" style="margin-top:.5rem;">
                Continue Shopping
            </a>
        </aside>
    </div>

<?php endif; ?>
</div>

<script>
const BASE_URL  = '<?= BASE_URL ?>';

function showToast(msg, type = 'success') {
    let t = document.getElementById('toast');
    if (!t) {
        t = document.createElement('div');
        t.id = 'toast';
        document.body.appendChild(t);
    }
    t.textContent = msg;
    t.className = 'toast toast--' + type + ' toast--show';
    clearTimeout(t._timer);
    t._timer = setTimeout(() => t.classList.remove('toast--show'), 3000);
}

function recalcTotals() {
    let grandTotal = 0, count = 0;
    document.querySelectorAll('.cart-row').forEach(row => {
        const qty   = parseInt(row.querySelector('.qty-input').value) || 0;
        const price = parseFloat(row.querySelector('.qty-input').dataset.price) || 0;
        const sub   = qty * price;
        row.querySelector('.cart-row__subtotal').textContent = '৳' + sub.toFixed(2);
        grandTotal += sub;
        count      += qty;
    });
    document.getElementById('cartTotal').textContent   = '৳' + grandTotal.toFixed(2);
    document.getElementById('summaryCount').textContent = count;
}

document.querySelectorAll('.cart-row').forEach(row => {
    const cartId  = row.dataset.cartId;
    const stock   = parseInt(row.dataset.stock);
    const input   = row.querySelector('.qty-input');
    const errSpan = row.querySelector('.qty-error');

    function setQty(val) {
        const v = parseInt(val);
        if (isNaN(v) || v < 1) { errSpan.textContent = 'Min quantity is 1.'; errSpan.style.display='block'; return; }
        if (v > stock)          { errSpan.textContent = `Max stock: ${stock}.`; errSpan.style.display='block'; return; }
        errSpan.style.display = 'none';

        fetch(BASE_URL + '/api/cart_update.php', {
            method:  'POST',
            headers: { 'Content-Type': 'application/json'},
            body:    JSON.stringify({ cart_id: parseInt(cartId), quantity: v }),
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                input.value = v;
                recalcTotals();
                // Update navbar badge
                document.querySelectorAll('.cart-badge').forEach(b => b.textContent = data.cart_count);
                showToast('Cart updated.');
            } else {
                showToast(data.error || data.message, 'error');
            }
        })
        .catch(() => showToast('Network error.', 'error'));
    }

    row.querySelector('.qty-btn--inc').addEventListener('click', () => setQty(parseInt(input.value) + 1));
    row.querySelector('.qty-btn--dec').addEventListener('click', () => setQty(parseInt(input.value) - 1));
    input.addEventListener('change', () => setQty(input.value));

    row.querySelector('.remove-btn').addEventListener('click', () => {
        if (!confirm('Remove this item from cart?')) return;
        fetch(BASE_URL + '/api/cart_remove.php', {
            method:  'DELETE',
            headers: { 'Content-Type': 'application/json'},
            body:    JSON.stringify({ cart_id: parseInt(cartId) }),
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                row.remove();
                recalcTotals();
                document.querySelectorAll('.cart-badge').forEach(b => b.textContent = data.cart_count);
                showToast('Item removed.');
                if (!document.querySelector('.cart-row')) location.reload();
            } else {
                showToast(data.error || data.message, 'error');
            }
        })
        .catch(() => showToast('Network error.', 'error'));
    });
});
</script>

<?php require ROOT_PATH . '/views/ashiqur/footer.php'; ?>
