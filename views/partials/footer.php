<footer>
    <p>Copyright <?php echo date('Y'); ?> Online Medicine Shop</p>
</footer>
<script>
(function () {
    function updateCartBadges(count) {
        document.querySelectorAll('.cart-badge').forEach(function (b) {
            b.textContent = count;
            b.style.display = count > 0 ? '' : 'none';
        });
    }

    function showCartToast(msg, isError) {
        var t = document.getElementById('cart-toast');
        if (!t) {
            t = document.createElement('div');
            t.id = 'cart-toast';
            t.className = 'toast';
            document.body.appendChild(t);
        }
        t.textContent = msg;
        t.className = 'toast toast--' + (isError ? 'error' : 'success') + ' toast--show';
        clearTimeout(t._timer);
        t._timer = setTimeout(function () { t.classList.remove('toast--show'); }, 3000);
    }

    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.med-card__cart');
        if (!btn || btn.tagName !== 'BUTTON') return;

        var card  = btn.closest('.med-card');
        var qtyEl = card ? card.querySelector('.med-card__qty') : null;
        var qty   = qtyEl ? parseInt(qtyEl.value, 10) : 1;
        var stock = parseInt(btn.dataset.stock || '0', 10);
        var medId = parseInt(btn.dataset.id || '0', 10);

        if (!medId || isNaN(qty) || qty < 1) {
            showCartToast('Enter a valid quantity.', true);
            return;
        }
        if (qty > stock) {
            showCartToast('Only ' + stock + ' in stock.', true);
            return;
        }

        btn.disabled = true;
        fetch('api/cart_add.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ medicine_id: medId, quantity: qty })
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            btn.disabled = false;
            if (data.success) {
                updateCartBadges(data.cart_count);
                showCartToast('Added to cart!');
            } else {
                showCartToast(data.message || 'Could not add to cart.', true);
            }
        })
        .catch(function () {
            btn.disabled = false;
            showCartToast('Network error.', true);
        });
    });
})();
</script>
</body>
</html>
